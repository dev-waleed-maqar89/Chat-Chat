<script setup>
import { onMounted, ref, reactive } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import $ from "jquery";
const emit = defineEmits(["selectUser"]);
const route = useRoute();
const users = ref([]);
const formData = reactive({
    search: "",
});
const getUsers = () => {
    axios.post("/users", formData).then((response) => {
        users.value = response.data.users;
    });
};
const openChat = (user) => {
    emit("selectUser", user);
};
</script>
<template>
    <div class="users-container">
        <dev class="form-group my-2 users-index-search">
            <input
                type="search"
                placeholder="Search"
                class="form-control"
                v-model="formData.search"
                @keyup="getUsers"
            />
        </dev>
        <dev class="users-list" v-if="users.length > 0">
            <div class="single-user-card" v-for="user in users">
                <dev class="single-user-card-link" @click="openChat(user)">
                    <span>{{ user.name }}</span>
                    <span>{{ user.email }}</span>
                </dev>
            </div>
        </dev>
        <dev class="users-list text text-center text-light" v-else>
            <span>No users found</span>
        </dev>
        <dev
            class="btn btn-secondary mt-2 d-block"
            @click="$emit('stopNewMessage', false)"
            >close</dev
        >
    </div>
</template>
