const obj = {
    placeholderText: '',
    placeholderExamples: [
        'Contoh: "Saya batuk..."',
        'Contoh: "Perut kanan bawah..."'
    ],
    currentPlaceholderIdx: 0,
    typewriterPlaceholder() {
        let currentExample = this.placeholderExamples[this.currentPlaceholderIdx];
        let isDeleting = false;
        let charIdx = 0;
        let typingSpeed = 70;

        const type = () => {
            currentExample = this.placeholderExamples[this.currentPlaceholderIdx];
            if (isDeleting) {
                this.placeholderText = currentExample.substring(0, charIdx - 1);
                charIdx--;
                typingSpeed = 30;
            } else {
                this.placeholderText = currentExample.substring(0, charIdx + 1);
                charIdx++;
                typingSpeed = 50;
            }

            console.log(this.placeholderText);
            if (charIdx > 15) return; // limit for test

            if (!isDeleting && charIdx === currentExample.length) {
                typingSpeed = 3500;
                isDeleting = true;
            } else if (isDeleting && charIdx === 0) {
                isDeleting = false;
                this.currentPlaceholderIdx = (this.currentPlaceholderIdx + 1) % this.placeholderExamples.length;
                typingSpeed = 500;
            }

            setTimeout(type, typingSpeed);
        };
        type();
    }
};
obj.typewriterPlaceholder();
