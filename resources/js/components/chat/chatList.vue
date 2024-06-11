<script setup>
import { onMounted, ref, reactive } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import $ from "jquery";
import singlechat from "../chat/singleChat.vue";
const route = useRoute();
const emit = defineEmits(["selectUser"]);
const partner = ref({});
const props = defineProps({
    authUser: Object,
    otherUser: Object,
});
const chatList = ref([]);
const onlineUsers = reactive([]);
const chatPartner = ref({});
const key_word = ref("");
const chatSearch = () => {
    axios
        .post("/conversations/search", { key_word: key_word.value })
        .then((response) => {
            chatList.value = response.data.chats;
        })
        .catch((error) => {
            errors.value = error.response.data.errors;
        });
};

onMounted(() => {
    getChatList();
    Echo.join("onlineUser")
        .here((users) => {
            users.forEach((user) => {
                let index = user.partners.indexOf(props.authUser.id);
                if (index != -1 && !isOnline(user.id)) {
                    onlineUsers.unshift(user.id);
                }
            });
        })
        .joining((user) => {
            let index = user.partners.indexOf(props.authUser.id);
            if (index != -1 && !isOnline(user.id)) {
                onlineUsers.unshift(user.id);
            }
        })
        .leaving((user) => {
            onlineUsers.forEach((id, index) => {
                if (id == user.id) {
                    onlineUsers.splice(index, 1);
                }
            });
        })
        .error((err) => {
            console.log(err);
        });
});
const getChatList = () => {
    axios.get("/conversations").then((response) => {
        chatList.value = response.data.chats;
        Echo.private("NewChatForUser." + props.authUser.id).listen(
            ".NewChat",
            (data) => {
                chatList.value.unshift(data.conversation);
            }
        );
        Echo.private("NewChatFromUser." + props.authUser.id).listen(
            ".NewChat",
            (data) => {
                chatList.value.unshift(data.conversation);
            }
        );
        Echo.private("NewMessageForUser." + props.authUser.id).listen(
            ".NewMessageInChat",
            (data) => {
                let chat = data.conversation;
                let index = chatList.value.findIndex(
                    (item) => item.id == chat.id
                );
                if (index != -1) {
                    chatList.value.splice(index, 1);
                    chatList.value.unshift(chat);
                }
            }
        );
        Echo.private("NewMessageFromUser." + props.authUser.id).listen(
            ".NewMessageInChat",
            (data) => {
                let chat = data.conversation;
                let index = chatList.value.findIndex(
                    (item) => item.id == chat.id
                );
                if (index != -1) {
                    chatList.value.splice(index, 1);
                    chatList.value.unshift(chat);
                }
            }
        );
    });
};

const openNewChat = (initiator, receiver) => {
    if (initiator.id == props.authUser.id) {
        partner.value = receiver;
    } else {
        partner.value = initiator;
    }
    emit("selectUser", partner.value);
};

const isOnline = (id) => {
    return onlineUsers.indexOf(id) != -1 ? true : false;
};
</script>
<template>
    <div class="form-group mb-2 chat-list-search">
        <input
            type="search"
            placeholder="Search"
            class="form-control"
            v-model="key_word"
            @input="chatSearch"
        />
    </div>
    <div
        class="mb-1 single-chat-header"
        :class="
            chat.initiator.id == props.otherUser.id ||
            chat.receiver.id == props.otherUser.id
                ? 'current'
                : ''
        "
        v-for="chat in chatList"
        @click="openNewChat(chat.initiator, chat.receiver)"
    >
        <img
            :src="chat.receiver.profile_image"
            class="single-chat-sender-image"
            v-if="chat.initiator.id == props.authUser.id"
        />
        <img
            :src="chat.initiator.profile_image"
            class="single-chat-sender-image"
            v-else
        />
        <div class="single-chat-sender-info">
            <div
                class="single-chat-user-name"
                v-if="chat.initiator.id == props.authUser.id"
                :class="isOnline(chat.receiver.id) ? 'online' : 'offline'"
            >
                {{ chat.receiver.name }}
            </div>
            <div
                class="single-chat-user-name"
                v-else
                :class="isOnline(chat.initiator.id) ? 'online' : 'offline'"
            >
                {{ chat.initiator.name }}
            </div>
        </div>
        <div class="single-chat-message-body" v-if="chat.accepted">
            <div class="single-chat-message-status"></div>
            <p class="single-chat-last-message">
                {{ chat.last_message }}
            </p>
        </div>
        <div class="single-chat-message-body" v-else>
            <div class="single-chat-message-status"></div>
            <p
                class="single-chat-last-message"
                v-if="chat.initiator.id == props.authUser.id"
            >
                Waiting for other user acciptance
            </p>
            <p class="single-chat-last-message" v-else>
                {{ chat.initiator.name }} wants to chat.
            </p>
        </div>
    </div>
</template>
