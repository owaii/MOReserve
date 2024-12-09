document.addEventListener("DOMContentLoaded", () => {
    const textElement = document.querySelector(".typewriter-text");
    const text = textElement.getAttribute("text");
    const delay = parseInt(textElement.getAttribute("delay")) || 100;

    let index = 0;

    function typeWriter() {
      if (index < text.length) {
        textElement.textContent += text.charAt(index);
        index++;
        setTimeout(typeWriter, delay);
      } else {
        textElement.classList.add("no-cursor");
      }
    }

    typeWriter();
  });