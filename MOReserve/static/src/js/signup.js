document.addEventListener("DOMContentLoaded", () => {
    let currentStep = 1;
    const steps = document.querySelectorAll(".step");
    const progressBar = document.querySelector(".progress-bar");
    const totalSteps = steps.length;

    const updateStep = (stepChange) => {
      if ((currentStep + stepChange > 0) && (currentStep + stepChange <= totalSteps)) {
        steps[currentStep - 1].classList.add("hidden");
        currentStep += stepChange;
        steps[currentStep - 1].classList.remove("hidden");
        progressBar.style.width = `${(currentStep / totalSteps) * 100}%`;
      }
    };

    document.querySelectorAll(".next-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const stepContainer = steps[currentStep - 1];
        const requiredFields = stepContainer.querySelectorAll("input:required");
        let valid = true;

        requiredFields.forEach(input => {
          if (!input.checkValidity()) {
            input.classList.add("border-red-500");
            valid = false;
          } else {
            input.classList.remove("border-red-500");
          }
        });

        if (currentStep === 3) { // Check email confirmation
          const email = stepContainer.querySelector("#email");
          const confirmEmail = stepContainer.querySelector("#confirm-email");
          if (email.value !== confirmEmail.value) {
            confirmEmail.classList.add("border-red-500");
            valid = false;
          }
        }

        if (valid) updateStep(1);
      });
    });

    document.querySelectorAll(".back-btn").forEach(btn => {
      btn.addEventListener("click", () => updateStep(-1));
    });

    // Format phone number
    document.querySelector("#phone-number").addEventListener("input", (e) => {
      let input = e.target.value.replace(/\D/g, "").slice(0, 9);
      e.target.value = input.replace(/(\d{3})(\d{3})(\d{3})/, "$1 $2 $3");
    });
  });