<script setup>
import { onMounted, ref, reactive } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import $ from "jquery";
const route = useRoute();
const props = defineProps({
    authUser: Object,
    otherUser: Object,
});
const conversation = ref({});
const messages = ref([]);
const formData = reactive({
    message: "",
});
const errors = ref([]);
const getCurrentChat = () => {
    axios.get("/users/" + props.otherUser.id + "/chat").then((response) => {
        conversation.value = response.data.chat;
        messages.value = response.data.messages;
        Echo.private("NewMessageInChat." + conversation.value.id).listen(
            ".ReceiveNewMessage",
            (data) => {
                messages.value.push(data.message);
            }
        );
        Echo.private("ChatAccepted." + conversation.value.id).listen(
            ".AcceptedChat",
            (data) => {
                conversation.value.accepted = 1;
            }
        );
        Echo.private("MarkAsRead." + conversation.value.id).listen(
            ".ChatMarkedAsRead",
            (data) => {
                let unreadMessages = messages.value.filter(
                    (message) => message.sender.id == props.authUser.id
                );
                if (unreadMessages.length > 0) {
                    unreadMessages.forEach((message) => {
                        message.is_read = 1;
                    });
                }
            }
        );
    });
};
const sendMessage = () => {
    formData.conversationId = conversation.value.id;
    axios
        .post("/conversations/sendMessage/", formData)
        .then((response) => {
            var newMessage = response.data.message;
            formData.message = "";
            errors.value = [];
            messages.value.push(newMessage);
        })
        .catch((error) => {
            errors.value = error.response.data.errors;
        });
};

const acceptChat = () => {
    axios
        .post("/conversations/" + conversation.value.id + "/accept")
        .then((response) => {
            conversation.value.accepted = 1;
        });
};

const markAsRead = () => {
    axios.post("/conversations/" + conversation.value.id + "/markAsRead");
};
onMounted(() => {
    getCurrentChat();
    $("#message").focus();
});
</script>
<template>
    <div class="current-conversation" id="currentConversation">
        <div class="px-3 current-conversation-messages-container">
            <div v-if="messages.length > 0">
                <div v-for="message in messages" :key="message.id">
                    <div
                        class="mb-2 chat-message-sent"
                        v-if="message.sender.id == props.authUser.id"
                    >
                        <span class="current-chat-message-body"
                            >{{ message.message }}
                        </span>
                        <span
                            class="current-chat-message-read"
                            v-if="message.is_read"
                        >
                            &#x2714;
                        </span>
                    </div>
                    <div class="mb-2 chat-message-received" v-else>
                        <img
                            :src="message.sender.profile_image"
                            class="current-chat-sender-image"
                        />
                        <span class="current-chat-message-body"
                            >{{ message.message }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-center no-conversation" v-else>
                <img
                    src="/images/main/no-conversation.jpg"
                    class="no-conversation-image"
                />
                <h4 class="mt-3">Send a message to start conversation</h4>
            </div>
        </div>
        <form
            class="mt-2 px-2 chat-new-message-form"
            @submit.prevent="sendMessage"
            v-if="conversation.accepted == 1"
        >
            <div v-if="errors.message">
                <div
                    class="text text-danger form-error"
                    v-for="error in errors.message"
                >
                    *{{ error }}
                </div>
            </div>
            <div class="mb-2 form-group">
                <input
                    type="text"
                    name="message"
                    id="message"
                    placeholder="Enter message"
                    class="form-control"
                    v-model="formData.message"
                    @click="markAsRead"
                />
                <input type="submit" class="btn btn-primary" value="&#10148;" />
            </div>
        </form>
        <div class="text-center chat-waiting-for-acceptance" v-else>
            <small v-if="authUser.id == conversation.initiator_id"
                >Waiting for other participant acceptance</small
            >
            <button class="btn btn-primary" @click="acceptChat" v-else>
                Accept
            </button>
        </div>
    </div>
</template>
