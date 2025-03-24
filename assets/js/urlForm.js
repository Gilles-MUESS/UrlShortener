(function () {
  const card = document.getElementById("shortenCard");
  const form = document.getElementById("shortenForm");
  const input = document.getElementById("urlInput");
  const submitBtn = document.getElementById("shortenBtn");

  const AJAX_URL = "/ajax/shorten";

  const ERROR_MESSAGES = {
    INVALID_ARG_URL:
      "Impossible de raccourcir ce lien. L'URL n'est pas valide.",
    MISSING_ARG_URL: "Veuillez fournir une URL valide.",
  };

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    fetch(AJAX_URL, {
      method: "POST",
      body: new FormData(e.target),
    })
      .then((response) => response.json())
      .then(handleData);
  });

  const handleData = (data) => {
    if (data.statusCode !== 200) {
      handleError(data);
    }

    input.value = data.shortUrl;
    submitBtn.innerText = "Copier";

    submitBtn.addEventListener(
      "click",
      (e) => {
        e.preventDefault();
        navigator.clipboard.writeText(input.value);
        submitBtn.innerText = "Copié !";

        setTimeout(() => {
          submitBtn.innerText = "Réduire l'URL";
          input.value = "";
        }, 2000);
      },
      { once: true }
    );
  };

  const handleError = (data) => {
    const alert = document.createElement("div");
    alert.classList.add("alert", "alert-danger", "mt-2");
    alert.innerText = ERROR_MESSAGES[data.statusText];
    card.after(alert);
  };
})();
