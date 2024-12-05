document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const steps = document.querySelectorAll(".step");
  const progressBar = document.querySelector(".progress-bar");
  const totalSteps = steps.length;

  const updateProgressBar = () => {
    const progress = (currentStep / totalSteps) * 100;
    progressBar.style.width = `${progress}%`;
  };

  const updateStep = (stepChange) => {
    if (currentStep + stepChange > 0 && currentStep + stepChange <= totalSteps) {
      steps[currentStep - 1].classList.add("hidden");
      currentStep += stepChange;
      steps[currentStep - 1].classList.remove("hidden");
      updateProgressBar();
    }
  };
  
  const validateCurrentStep = () => {
  const stepContainer = steps[currentStep - 1];
  const visibleInputs = stepContainer.querySelectorAll("input:required:not([type='hidden']), select:required");
  let isValid = true;

  const checkServerValidation = async (value, id) => {
    console.log("Checking: " + value + " for id: " + id);

    fetch(`static/src/php/checkValues.php?value=${encodeURIComponent(value)}&id=${encodeURIComponent(id)}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.valid) {
                console.log(data.valid);
                return data.valid;
            } else {
                console.error("Error: " + data.message);
                return false;
            }
        }
    })
    .catch(error => {
        console.error("Request failed", error);
        return false;
    });
  };

  visibleInputs.forEach(input => {
    if (!input.checkValidity()) {
      input.classList.add("border-red-500");
      console.log(`Validation failed for: ${input.placeholder || input.name || 'Unnamed field'}`);
      console.log(`Value: "${input.value}"`);
      console.log(`Error: ${input.validationMessage}`);
      isValid = false;
    } else {
      input.classList.remove("border-red-500");
    }

    if (checkServerValidation(input.value, input.id) == false) {
      input.classList.add("border-red-500");
      console.log(`Validation failed for: ${input.placeholder || input.name || 'Unnamed field'}`);
      console.log(`Value: "${input.value}"`);
      console.log(`Error: ${input.validationMessage}`);
      isValid = false;
    } else {
      input.classList.remove("border-red-500");
    }

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

    if (input.dataset.mask === "letters-only" && /[^a-zA-Z\s]/.test(input.value)) {
      input.classList.add("border-red-500");
      console.log(`Invalid characters in: "${input.value}". Only letters and spaces allowed.`);
      isValid = false;
    }

    if (input.dataset.mask === "numbers-only" && /\D/.test(input.value)) {
      input.classList.add("border-red-500");
      console.log(`Invalid characters in: "${input.value}". Only numbers allowed.`);
      isValid = false;
    }
  });

  if (isValid) {
    console.log(`Step ${currentStep} validation passed!`);
  } else {
    console.log(`Validation failed on Step ${currentStep}.`);
  }

  return isValid;
  };

  
  document.querySelectorAll(".next-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      console.log(`Attempting to validate Step ${currentStep}...`);
      if (validateCurrentStep()) {
        console.log(`Step ${currentStep} is valid. Moving to the next step.`);
        updateStep(1);
      } else {
        console.log(`Validation failed on Step ${currentStep}.`);
      }
    });
  });

  document.querySelectorAll(".back-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      updateStep(-1);
    });
  });

  const phoneNumberInput = document.querySelector("#phoneNumber");
  if (phoneNumberInput) {
    phoneNumberInput.addEventListener("input", (e) => {
      const input = e.target.value.replace(/\D/g, "").slice(0, 9);
      e.target.value = input.replace(/(\d{3})(\d{3})(\d{3})/, "$1 $2 $3");
    });
  }

  document.querySelectorAll("input[data-mask='postal-code']").forEach(input => {
    input.addEventListener("input", (e) => {
      const value = e.target.value.replace(/\D/g, "").slice(0, 5);
      e.target.value = value.replace(/(\d{2})(\d{3})/, "$1-$2");
    });
  });

  document.querySelectorAll("input[data-mask='letters-only']").forEach(input => {
    input.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, "").slice(0, 32);
    });
  });

  document.querySelectorAll("input[data-mask='numbers-only']").forEach(input => {
    input.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/\D/g, "").slice(0, 3);
    });
  });

  updateProgressBar();
});
