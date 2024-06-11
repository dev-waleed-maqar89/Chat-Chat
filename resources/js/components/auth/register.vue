<script setup>
import { onMounted, ref, reactive } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import Navbar from "../layouts/navbar.vue";

const router = useRouter();
const user = ref({});
const errors = ref([]);
const formData = new FormData();
const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
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
const register = () => {
    formData.append("name", name.value);
    formData.append("email", email.value);
    formData.append("password", password.value);
    formData.append("password_confirmation", password_confirmation.value);
    var uploadedimage = document.querySelector("#image").files[0];
    if (uploadedimage) {
        formData.append("image", uploadedimage);
    }
    axios
        .post("/register", formData)
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
        <form enctype="multipart/form-data">
            <div class="mb-2 form-group">
                <input
                    type="text"
                    name="name"
                    placeholder="Your name"
                    class="form-control"
                    v-model="name"
                />
                <div v-if="errors.name">
                    <div
                        class="text text-danger form-error"
                        v-for="error in errors.name"
                    >
                        *{{ error }}
                    </div>
                </div>
            </div>
            <div class="mb-2 form-group">
                <input
                    type="email"
                    name="email"
                    placeholder="Your E-mail"
                    class="form-control"
                    v-model="email"
                />
                <div v-if="errors.email">
                    <div
                        class="text text-danger form-error"
                        v-for="error in errors.email"
                    >
                        *{{ error }}
                    </div>
                </div>
            </div>
            <div class="mb-2 form-group">
                <input
                    type="password"
                    name="password"
                    placeholder="Enter Password"
                    class="form-control"
                    v-model="password"
                />
                <div v-if="errors.password">
                    <div
                        class="text text-danger form-error"
                        v-for="error in errors.password"
                    >
                        *{{ error }}
                    </div>
                </div>
            </div>
            <div class="mb-2 form-group">
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Re-enter password"
                    class="form-control"
                    v-model="password_confirmation"
                />
            </div>
            <div class="form-group">
                <input
                    type="file"
                    name="image"
                    class="form-control"
                    id="image"
                />
            </div>
            <div
                class="text text-danger form-error"
                v-for="error in errors.image"
            >
                {{ error }}
            </div>
            <div class="m-2 have-acc">
                Already have account?
                <router-link to="/login">Login</router-link>
            </div>
            <div class="mb-2 form-group">
                <input
                    type="submit"
                    value="Register"
                    class="form-control btn btn-success"
                    @click.prevent="register"
                />
            </div>
        </form>
    </div>
</template>
