document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const steps = document.querySelectorAll(".step");
  const progressBar = document.querySelector(".progress-bar");
  const totalSteps = steps.length;

  // Update the progress bar based on current step
  const updateProgressBar = () => {
    const progress = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${progress}%`;
  };

  // Update the current step (next or previous)
  const updateStep = (stepChange) => {
    if (currentStep + stepChange > 0 && currentStep + stepChange <= totalSteps) {
      steps[currentStep - 1].classList.add("hidden");
      currentStep += stepChange;
      steps[currentStep - 1].classList.remove("hidden");
      updateProgressBar();
    }
  };

  // Asynchronous server validation
  const checkServerValidation = async (value, id) => {
    try {
      const response = await fetch(`static/src/php/checkValues.php?value=${encodeURIComponent(value)}&id=${encodeURIComponent(id)}`, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      const data = await response.json();
      return data.valid;
    } catch (error) {
      console.error("Server validation error", error);
      return false;
    }
  };

  // Validate the current step
  const validateCurrentStep = async () => {
    const stepContainer = steps[currentStep - 1];
    const visibleInputs = stepContainer.querySelectorAll("input:required:not([type='hidden']), select:required");
    let isValid = true;

    for (let input of visibleInputs) {
      // Check HTML5 form validity
      if (!input.checkValidity()) {
        input.classList.add("border-red-500");
        console.log(`Validation failed for: ${input.placeholder || input.name || 'Unnamed field'}`);
        console.log(`Value: "${input.value}"`);
        console.log(`Error: ${input.validationMessage}`);
        isValid = false;
      } else {
        input.classList.remove("border-red-500");
      }

      if (input.id != "" && (currentStep == 2 && input.id != "confirm-email") || currentStep == 3 || (currentStep == 7 && input.id != "password")) {
        const isValidServer = await checkServerValidation(input.value, input.id);
        if (!isValidServer) {
          console.log(`Failed for: ${input.value} in ${input.id}`);
          input.classList.add("border-red-500");
          isValid = false;
        } else {
          console.log(`Passed for: ${input.value} in ${input.id}`);
        }
      }

      // Custom validation for postal code format
      if (input.dataset.mask === "postal-code") {
        const postalCodeRegex = /^\d{2}-\d{3}$/;
        if (!postalCodeRegex.test(input.value)) {
          input.classList.add("border-red-500");
          console.log(`Invalid postal code format: "${input.value}". Expected format: XX-XXX`);
          isValid = false;
        } else {
          input.classList.remove("border-red-500");
        }
      }

      // Custom validation for letters-only input
      if (input.dataset.mask === "letters-only" && /[^a-zA-Z\s]/.test(input.value)) {
        input.classList.add("border-red-500");
        console.log(`Invalid characters in: "${input.value}". Only letters and spaces allowed.`);
        isValid = false;
      }

      // Custom validation for numbers-only input
      if (input.dataset.mask === "numbers-only" && /\D/.test(input.value)) {
        input.classList.add("border-red-500");
        console.log(`Invalid characters in: "${input.value}". Only numbers allowed.`);
        isValid = false;
      }
    }

    // Feedback for validation status
    if (isValid) {
      console.log(`Step ${currentStep} validation passed!`);
    } else {
      console.log(`Validation failed on Step ${currentStep}.`);
    }

    return isValid;
  };

  // Move to next step
  document.querySelectorAll(".next-btn").forEach(btn => {
    btn.addEventListener("click", async () => {
      console.log(`Attempting to validate Step ${currentStep}...`);
      if (await validateCurrentStep()) {
        console.log(`Step ${currentStep} is valid. Moving to the next step.`);
        updateStep(1);
      } else {
        console.log(`Validation failed on Step ${currentStep}.`);
      }
    });
  });

  // Move to previous step
  document.querySelectorAll(".back-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      updateStep(-1);
    });
  });

  // Mask input for phone number
  const phoneNumberInput = document.querySelector("#phone-number");
  if (phoneNumberInput) {
    phoneNumberInput.addEventListener("input", (e) => {
      const input = e.target.value.replace(/\D/g, "").slice(0, 9);
      e.target.value = input.replace(/(\d{3})(\d{3})(\d{3})/, "$1 $2 $3");
    });
  }

  // Mask input for postal code (XX-XXX format)
  document.querySelectorAll("input[data-mask='postal-code']").forEach(input => {
    input.addEventListener("input", (e) => {
      const value = e.target.value.replace(/\D/g, "").slice(0, 5);
      e.target.value = value.replace(/(\d{2})(\d{3})/, "$1-$2");
    });
  });

  // Mask input for letters-only
  document.querySelectorAll("input[data-mask='letters-only']").forEach(input => {
    input.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, "").slice(0, 32);
    });
  });

  // Mask input for numbers-only
  document.querySelectorAll("input[data-mask='numbers-only']").forEach(input => {
    input.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/\D/g, "").slice(0, 3);
    });
  });

  // Initial progress bar update
  updateProgressBar();
});
