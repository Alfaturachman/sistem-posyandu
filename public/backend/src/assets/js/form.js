function setActiveStep(step) {
    // Hapus class active dari semua langkah dan baris
    document
        .querySelectorAll('.step-circle')
        .forEach((el) => el.classList.remove('active'));
    document
        .querySelectorAll('.step-line')
        .forEach((el) => el.classList.remove('active'));
    document
        .querySelectorAll('.row[data-step]')
        .forEach((row) => row.classList.add('d-none'));

    // Tambahkan class active pada langkah yang sesuai
    let currentStep = document.querySelector(
        `.step-circle[data-step="${step}"]`
    );
    while (currentStep) {
        if (currentStep.classList.contains('step-circle')) {
            currentStep.classList.add('active');
        }
        if (currentStep.classList.contains('step-line')) {
            currentStep.classList.add('active');
        }
        currentStep = currentStep.previousElementSibling;
    }

    // Tampilkan row sesuai langkah aktif
    const activeRow = document.querySelector(`.row[data-step="${step}"]`);
    if (activeRow) {
        activeRow.classList.remove('d-none');
    }
}

function nextStep(currentStep, nextStep) {
    console.log(`Navigating from ${currentStep} to ${nextStep}`);
    const form = document.querySelector(
        `.row[data-step="${currentStep}"] form`
    );
    if (form && !form.checkValidity()) {
        form.reportValidity();
        return;
    }
    setActiveStep(nextStep);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-next-step]').forEach((button) => {
        button.addEventListener('click', function () {
            const currentStep = this.getAttribute('data-current-step');
            const targetStep = this.getAttribute('data-next-step');
            nextStep(currentStep, targetStep);
        });
    });
});

// Set langkah awal
setActiveStep('info_matkul');
