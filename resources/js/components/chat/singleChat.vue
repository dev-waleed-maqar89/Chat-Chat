<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import $ from "jquery";
const route = useRoute();
const props = defineProps({
    chat: Object,
});
const partner = ref({});
const profileImage = ref("");
onMounted(() => {
    getChat();
});
const getChat = () => {
    let id = props.chat.id;
    axios.get("/conversations/" + id).then((response) => {
        partner.value = response.data.partner;
        profileImage.value = response.data.profileImage;
    });
};
</script>
<template>
    <div class="mb-1 single-chat-header">
        <img :src="profileImage" class="single-chat-sender-image" />
        <div class="single-chat-sender-info">
            <div class="single-chat-sender-name">
                {{ partner.name }}
            </div>
            <div class="single-chat-sender-status">Online</div>
        </div>
        <div class="single-chat-message-body">
            <div class="single-chat-message-status"></div>
            <p class="single-chat-last-message">
                New very very very very long message here
            </p>
        </div>
    </div>
</template>
