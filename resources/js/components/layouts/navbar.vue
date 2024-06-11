<script setup>
import { onMounted, ref, reactive } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const user = ref({});
const router = useRouter();
const emit = defineEmits(["createMessage"]);
const props = defineProps({
    otherUser: Object,
});
const getAuthUser = () => {
    axios.get("/getAuthUser").then((response) => {
        user.value = response.data.user;
    });
};
onMounted(() => {
    getAuthUser();
});
const Logout = () => {
    axios.post("/logout").then((response) => {
        router.push("/login");
    });
};
</script>
<template>
    <nav class="navbar-container">
        <ul class="main-navbar" v-if="user">
            <li>
                <a
                    class="navbar-link"
                    @click.prevent="$emit('createMessage', true)"
                    >+New
                </a>
            </li>
            <li>
                <a class="navbar-link" active-class="active" href="/logout"
                    >Logout</a
                >
            </li>
        </ul>

        <ul class="main-navbar" v-else>
            <li>
                <router-link
                    class="navbar-link"
                    active-class="active"
                    to="/login"
                    >Login</router-link
                >
            </li>
            <li>
                <router-link
                    class="navbar-link"
                    active-class="active"
                    to="/register"
                    >register</router-link
                >
            </li>
        </ul>
        <ul class="chat-navbar" v-if="otherUser && otherUser.id">
            <li>
                <a class="navbar-link" @click.prevent>
                    <img
                        :src="otherUser.profile_image"
                        class="current-chat-navbar-image"
                    />
                    {{ otherUser.name }}
                </a>
            </li>
        </ul>
    </nav>
</template>
