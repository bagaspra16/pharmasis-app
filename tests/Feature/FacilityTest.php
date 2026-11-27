<?php

namespace Tests\Feature;

use App\Services\Facilities\FacilityService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    private const HERE = ['lat' => -6.1751, 'lon' => 106.8272];

    private string $curated;

    protected function setUp(): void
    {
        parent::setUp();
        $this->curated = tempnam(sys_get_temp_dir(), 'fac');
        config([
            'cache.default' => 'array', 'facilities.enabled' => true, 'facilities.curated_path' => $this->curated,
            'facilities.overpass_urls' => ['https://map.test/api'], 'facilities.geocoder_url' => 'https://geo.test/search',
        ]);
        Cache::flush();
        RateLimiter::clear('geocoder');
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        @unlink($this->curated);
        parent::tearDown();
    }

    private function node(int $id, array $tags, float $north = 0.001, string $edited = '2026-01-01T00:00:00Z'): array
    {
        return ['type' => 'node', 'id' => $id, 'lat' => self::HERE['lat'] + $north, 'lon' => self::HERE['lon'], 'timestamp' => $edited, 'tags' => $tags];
    }

    private function fakeMap(array $elements, array $extra = []): void
    {
        Http::fake(['map.test/*' => Http::response(['elements' => $elements] + $extra)]);
    }

    private function find(string $category = 'all', int $radius = 5): array
    {
        return app(FacilityService::class)->search(self::HERE['lat'], self::HERE['lon'], $radius, $category)['results'];
    }

    public function test_missing_details_stay_empty_and_map_data_is_never_verified(): void
    {
        $this->fakeMap([$this->node(1, ['amenity' => 'pharmacy', 'name' => 'Apotek A'])]);

        $record = $this->find()[0];

        foreach (['address', 'phone', 'website', 'opening_hours', 'emergency', 'checked_on'] as $field) {
            $this->assertNull($record[$field], $field);
        }
        $this->assertSame('community', $record['status']);
        $this->assertSame('https://www.openstreetmap.org/node/1', $record['source_url']);
    }

    public function test_unusable_and_irrelevant_records_are_left_out(): void
    {
        $this->fakeMap([
            $this->node(1, ['amenity' => 'clinic']),
            $this->node(2, ['amenity' => 'clinic', 'name' => 'Klinik Hewan Sehat']),
            $this->node(3, ['amenity' => 'dentist', 'name' => 'Gigi Sehat']),
            $this->node(4, ['amenity' => 'clinic', 'name' => 'Far Clinic'], 0.2),
            $this->node(5, ['amenity' => 'clinic', 'name' => 'Near Clinic', 'website' => 'javascript:alert(1)', 'phone' => 'ask at desk']),
        ]);

        $found = $this->find('care');

        $this->assertSame(['Near Clinic'], array_column($found, 'name'));
        $this->assertNull($found[0]['website']);
        $this->assertNull($found[0]['phone']);
    }

    public function test_distance_order_categories_and_stale_flag(): void
    {
        $this->fakeMap([
            $this->node(1, ['amenity' => 'clinic', 'name' => 'Puskesmas Gambir'], 0.009, '2020-01-01T00:00:00Z'),
            $this->node(2, ['amenity' => 'hospital', 'name' => 'RS Dekat'], 0.001),
        ]);

        $all = $this->find();

        $this->assertSame(['RS Dekat', 'Puskesmas Gambir'], array_column($all, 'name'));
        $this->assertEqualsWithDelta(1.0, $all[1]['distance_km'], 0.02);
        $this->assertSame(['hospital', 'puskesmas'], array_column($all, 'category'));
        $this->assertTrue($all[1]['stale']);
        $this->assertFalse($all[0]['stale']);
    }

    public function test_emergency_search_puts_listed_departments_first_and_drops_those_without(): void
    {
        $this->fakeMap([
            $this->node(1, ['amenity' => 'hospital', 'name' => 'Closest Unknown'], 0.001),
            $this->node(2, ['amenity' => 'hospital', 'name' => 'Farther With ER', 'emergency' => 'yes'], 0.02),
            $this->node(3, ['amenity' => 'hospital', 'name' => 'No ER', 'emergency' => 'no'], 0.001),
            $this->node(4, ['amenity' => 'pharmacy', 'name' => 'Pharmacy'], 0.001),
        ]);

        $this->assertSame(['Farther With ER', 'Closest Unknown'], array_column($this->find('emergency'), 'name'));
    }

    public function test_duplicates_merge_but_distant_namesakes_stay(): void
    {
        $this->fakeMap([
            $this->node(1, ['amenity' => 'hospital', 'name' => 'RS Mitra']),
            $this->node(2, ['amenity' => 'hospital', 'name' => 'Rumah Sakit Mitra', 'phone' => '+62 21 123456'], 0.0015),
            $this->node(3, ['amenity' => 'pharmacy', 'name' => 'Apotek Chain'], 0.001),
            $this->node(4, ['amenity' => 'pharmacy', 'name' => 'Apotek Chain'], 0.02),
        ]);

        $found = $this->find();

        $this->assertCount(3, $found);
        $this->assertSame('+62 21 123456', collect($found)->firstWhere('category', 'hospital')['phone']);
    }

    public function test_verification_needs_a_source_and_a_recent_check(): void
    {
        $row = ['category' => 'clinic', 'lat' => self::HERE['lat'], 'lon' => self::HERE['lon'], 'status' => 'verified'];
        file_put_contents($this->curated, json_encode(['records' => [
            $row + ['name' => 'Proven', 'source_url' => 'https://example.org/a', 'checked_on' => date('Y-m-d')],
            ['name' => 'No Source', 'lat' => self::HERE['lat'] + 0.01] + $row + ['checked_on' => date('Y-m-d')],
            ['name' => 'Old Check', 'lat' => self::HERE['lat'] + 0.02] + $row + ['source_url' => 'https://example.org/b', 'checked_on' => '2020-01-01'],
        ]]));
        $this->fakeMap([]);

        $this->assertSame(
            ['Proven' => 'verified', 'No Source' => 'unverified', 'Old Check' => 'unverified'],
            array_column($this->find(), 'status', 'name')
        );
    }

    public function test_the_map_service_never_receives_the_exact_position_and_results_are_cached(): void
    {
        $this->fakeMap([]);
        $this->find();
        $this->find();

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => str_contains($request['data'], '-6.18,106.83')
            && !str_contains($request['data'], '6.1751'));
    }

    public function test_partial_map_answers_are_refused_and_an_old_copy_is_labelled(): void
    {
        Http::fake(['map.test/*' => Http::sequence()
            ->push(['elements' => [$this->node(1, ['amenity' => 'clinic', 'name' => 'A'])]])
            ->push(['elements' => [], 'remark' => 'runtime error: timeout'])]);
        $this->find();
        $this->travel(config('facilities.fresh_hours') + 1)->hours();

        $again = app(FacilityService::class)->search(self::HERE['lat'], self::HERE['lon'], 5, 'all');

        $this->assertTrue($again['meta']['old_copy']);
        $this->assertCount(1, $again['results']);
    }

    public function test_outage_says_unavailable_and_still_gives_an_emergency_number(): void
    {
        Http::fake(['map.test/*' => Http::response('', 504)]);

        $this->postJson('/facilities/search', self::HERE + ['country' => 'id'])
            ->assertStatus(503)
            ->assertJsonPath('error', 'unavailable')
            ->assertJsonPath('emergency.number', '119')
            ->assertJsonMissingPath('results');
    }

    public function test_search_rejects_bad_input_and_geocode_returns_choices(): void
    {
        $this->postJson('/facilities/search', ['lat' => 200, 'lon' => 0])->assertStatus(422);
        $this->postJson('/facilities/search', self::HERE + ['radius' => 500])->assertStatus(422);

        Http::fake(['geo.test/*' => Http::response([
            ['lat' => '-6.2', 'lon' => '106.8', 'display_name' => 'Menteng, Jakarta', 'boundingbox' => ['-6.21', '-6.18', '106.82', '106.85'], 'address' => ['country_code' => 'id']],
            ['lat' => '-6.9', 'lon' => '107.6', 'display_name' => 'Menteng, Bandung', 'boundingbox' => ['-6.901', '-6.899', '107.599', '107.601']],
        ])]);

        $this->postJson('/facilities/geocode', ['query' => 'Menteng'])->assertOk()
            ->assertJsonPath('places.0.country', 'id')
            ->assertJsonPath('places.0.wide', true)
            ->assertJsonPath('places.1.wide', false);
    }

    public function test_home_page_looks_up_the_visitor_address_only_with_consent(): void
    {
        Http::fake(['*' => Http::response(['status' => 'success', 'city' => 'Jakarta', 'regionName' => 'Jakarta', 'country' => 'Indonesia', 'results' => []])]);
        $lookup = fn ($request) => str_contains($request->url(), 'ip-api.com');

        $this->get('/')->assertOk();
        Http::assertNotSent($lookup);

        $this->withUnencryptedCookie('pharmasis_consent', 'accepted')->get('/')->assertOk();
        Http::assertSent($lookup);
    }

    public function test_pages_render_and_the_invented_list_route_is_gone(): void
    {
        $this->get('/facilities')->assertOk()->assertSee('facilityFinder', false);
        $this->post('/medicheck/nearby', ['location' => 'x'])->assertNotFound();

        config(['facilities.enabled' => false]);
        $this->postJson('/facilities/search', self::HERE)->assertNotFound();
    }
}
