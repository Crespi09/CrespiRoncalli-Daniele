<script setup lang="ts">

import axios, { AxiosError, type AxiosResponse } from 'axios';
import { RouterLink, useRouter } from 'vue-router';
import { setAccessToken } from '@/utils/auth';
import { computed, reactive, ref } from 'vue';
import { inject } from 'vue';
import { BehaviorSubject } from 'rxjs';


// PrimeVue imports
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';


const router = useRouter();

const loginForm = reactive({
  email: '',
  password: ''
});

const errors = ref<string[]>([]);

const currentUser$ = inject<BehaviorSubject<{ email: string }>>('currentUser$');

const isValid = computed(() => {

  if (!loginForm.email.trim() || !loginForm.password.trim()) {

    return false;
  }

  return true;

});

const login = () => {

  errors.value = [];

  axios.post(import.meta.env.VITE_API_URL + 'auth/signin', loginForm)
    .then((response: AxiosResponse) => {

      console.log(response);

      setAccessToken(response.data.access_token);

      currentUser$!.next({ email: loginForm.email });

      router.push('/');

    })
    .catch((e: AxiosError) => {
      errors.value.push('Credentials are not valid');
    })
    ;

}

defineExpose({ loginForm, errors, isValid, login });

</script>


<template>
  <div class="login-container">
    <div class="login-card-border">
      <div class="login-card">
        <h1 class="title">Login</h1>
        
        <form @submit.prevent="login">
          <div v-if="errors.length" class="error-messages">
            <p v-for="error in errors" :key="error" class="error">
              {{ error }}
            </p>
          </div>

          <div class="form-group">
            <FloatLabel>
              <InputText id="email" v-model="loginForm.email" class="w-full" />
              <label for="email">Email</label>
            </FloatLabel>
          </div>

          <div class="form-group">
            <FloatLabel>
              <Password v-model="loginForm.password" inputId="password" :feedback="false" class="w-full" toggleMask />
              <label for="password">Password</label>
            </FloatLabel>
          </div>

          <Button type="submit" :disabled="!isValid" label="Login" class="w-full" severity="primary" raised />
        </form>

        <RouterLink to="/register" class="register-link">Need an account? Register</RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-container {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  margin-top: 3rem;
  /* background-color: #faf9f9; */
}

/* @property --angle {
    syntax: '<angle>';
    initial-value: 0deg;
    inherits: false;
} */

.login-card-border {
  padding: 3px;
  border-radius: 30px;
  width: 90%;
  max-width: 400px;
  /* background: linear-gradient(var(--angle), #00bd7e 10%, rgba(33, 150, 243, 0) 50%); */
  /* animation: rotate 4s linear infinite; */

  background: linear-gradient(180deg, #00bd7e 10%, rgba(33, 150, 243, 0) 40%);

}

/* 
@keyframes rotate {
    to {
        --angle: 360deg;
    }
} */

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 30px;
  /* box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1); */
  width: 100%;
  /* background: linear-gradient(292deg, #dc3545 10%, rgba(33, 150, 243, 0) 30%); */
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
  font-weight: 600;
}

.form-group {
  margin-bottom: 1.5rem;
}

.error-messages {
  background-color: #fff5f5;
  border-left: 4px solid #dc3545;
  padding: 1rem;
  margin-bottom: 1.5rem;
  border-radius: 4px;
}

.error {
  color: #dc3545;
  margin: 0.2rem 0;
  font-size: 0.875rem;
}

.register-link {
  display: block;
  text-align: center;
  border-radius: 5px;
  margin-top: 1.5rem;
  color: #666;
  text-decoration: none;
  transition: color 0.2s;
}

.register-link:hover {
  color: #333;
}

:deep(.p-inputtext) {
  width: 100%;
}

:deep(.p-password) {
  width: 100%;
}

:deep(.p-button) {
  width: 100%;
  margin-top: 1rem;
}

/* Media queries for responsive design */
@media screen and (max-width: 480px) {
  .login-card {
    padding: 1.5rem;
  }

  .title {
    font-size: 1.25rem;
    margin-bottom: 1rem;
  }

  .form-group {
    margin-bottom: 1rem;
  }
}

@media screen and (min-width: 768px) {
  .login-card {
    padding: 3rem;
  }

  .title {
    font-size: 1.75rem;
    margin-bottom: 2rem;
  }
}
</style>