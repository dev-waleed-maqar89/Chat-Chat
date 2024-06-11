<script setup>
import { onMounted, ref, reactive } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import Navbar from "../layouts/navbar.vue";
const router = useRouter();
const user = ref({});
const errors = ref([]);
const formData = reactive({
    email: "",
    password: "",
});
const getAuthUser = () => {
    axios.get("/getAuthUser").then((response) => {
        user.value = response.data.user;
        if (user.value) {
            router.push("/");
        }
    });
};
onMounted(() => {
    getAuthUser();
});
const login = () => {
    axios
        .post("/login", formData)
        .then((response) => {
            router.push("/");
        })
        .catch((error) => {
            errors.value = error.response.data.errors;
        });
};
</script>
<template>
    <Navbar />
    <div class="mt-5 container">
        <div class="mb-2 form-group">
            <input
                type="email"
                name="email"
                placeholder="Your E-mail"
                class="form-control"
                v-model="formData.email"
            />
            <div v-if="errors.email">
                <div class="text text-danger form-error">
                    *{{ errors.email }}
                </div>
            </div>
        </div>
        <div class="mb-2 form-group">
            <input
                type="password"
                name="password"
                placeholder="Enter Password"
                class="form-control"
                v-model="formData.password"
            />
        </div>
        <div class="m-2 have-acc">
            Don't have account?
            <router-link to="/register">Register</router-link>
        </div>
        <div class="mb-2 form-group">
            <input
                type="submit"
                value="Login"
                class="form-control btn btn-success"
                @click.prevent="login"
            />
        </div>
    </div>
</template>
