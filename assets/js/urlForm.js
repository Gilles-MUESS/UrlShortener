(function () {
  const card = document.getElementById("shortenCard");
  const form = document.getElementById("shortenForm");
  const input = document.getElementById("urlInput");
  const submitBtn = document.getElementById("shortenButton");

  const AJAX_URL = "/ajax/shorten";

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
    console.log(data);
  };
})();
