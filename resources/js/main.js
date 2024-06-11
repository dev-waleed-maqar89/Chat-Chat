import $ from "jquery";
$(document).ready(() => {
    for (let i = 0; i < 50; i++) {
        let singleChatHeader = `<div class="mb-1 single-chat-header">
        <img src="https://picsum.photos/200/300" class="single-chat-sender-image">
        <div class="single-chat-sender-info">
        <div class="single-chat-sender-name">The name of the message sender</div>
        <div class="single-chat-sender-status">Online</div>
        </div>
        <div class="single-chat-message-body">
        <div class="single-chat-message-status"></div>
        <p class="single-chat-last-message">New very very very very long message here</p>
        </div>
        </div>`
        $("#userConversationList").append(singleChatHeader)
    }
})
