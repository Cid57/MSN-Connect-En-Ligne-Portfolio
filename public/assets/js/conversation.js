window.onload = function () {
  let messageContainer = document.querySelector(".message-container");
  if (messageContainer) {
    messageContainer.scrollTop = messageContainer.scrollHeight;
  }
};
