<script setup>
import { onMounted, ref, reactive } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import $ from "jquery";
import pusher from "pusher-js";
import Navbar from "../layouts/navbar.vue";
import usersindex from "../auth/index.vue";
import currentChat from "../chat/currentchat.vue";
import chatlist from "../chat/chatList.vue";
import { useRouter } from "vue-router";
const router = useRouter();
const usersIndexShow = ref(false);
var authUser = ref({});
const otherUser = ref({});
const currentChatShow = reactive({ value: false });
const getAuthUser = () => {
    axios.get("/getAuthUser").then((response) => {
        authUser.value = response.data.user;
        if (!authUser.value) {
            router.push("/login");
        }
    });
};
onMounted(() => {
    getAuthUser();
});
const createNewMessage = (value) => {
    usersIndexShow.value = value;
};

const openChat = (user) => {
    currentChatShow.value = false;
    setTimeout(() => {
        otherUser.value = user;
        usersIndexShow.value = false;
        currentChatShow.value = true;
    }, 0.005);
    return;
};
$(document).ready(() => {
    let triggered = true;
    $("*").on("keyup", function (event) {
        triggered = !triggered;
        // to prevent the event from being triggered twice
        if (triggered) {
            // Pressing escape to close popups
            if (event.keyCode == 27) {
                if (!usersIndexShow.value) {
                    currentChatShow.value = false;
                    otherUser.value = {};
                }
                usersIndexShow.value = false;
            }
        }
    });
});
</script>
<template>
    <Navbar @createMessage="createNewMessage" :otherUser="otherUser" />
    <div class="mt-1 main-container" v-if="authUser && authUser.id">
        <aside class="conversains-list" id="userConversationList">
            <chatlist
                :authUser="authUser"
                :otherUser="otherUser"
                @selectUser="openChat"
                :key="authUser.id"
            />
        </aside>
        <main class="current-conversation">
            <currentChat
                :authUser="authUser"
                :otherUser="otherUser"
                v-if="currentChatShow.value"
            />
            <div v-else class="text-center no-current-chat">
                <img
                    src="/images/main/contact.jpg"
                    class="no-current-chat-image"
                />
                <h4 class="mt-3">Select a user to start conversation</h4>
                <button class="btn btn-primary" @click="usersIndexShow = true">
                    Select User
                </button>
            </div>
            <usersindex
                v-if="usersIndexShow"
                @stopNewMessage="createNewMessage"
                @selectUser="openChat"
            />
        </main>
    </div>
</template>
