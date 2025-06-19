<script setup lang="ts">

import axios, { AxiosError, type AxiosResponse } from 'axios';
import { setAccessToken } from '@/utils/auth';
import { computed, inject, reactive, ref } from 'vue';
import { BehaviorSubject } from 'rxjs';
import { useRouter } from 'vue-router';

// primeVue import
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';


const router = useRouter();

const registerForm = reactive({
  name: '',
  surname: '',
  email: '',
  password: ''
});

const errors = ref<string[]>([]);

const currentUser$ = inject<BehaviorSubject<{ email: string }>>('currentUser$');

const isValid = computed(() => {
  if (
    !registerForm.name.trim() ||
    !registerForm.surname.trim() ||
    !registerForm.email.trim() ||
    !registerForm.password.trim()

  ) {
    return false;
  }

  return true;
})


const register = async () => {

  errors.value = [];

  registerForm.name = registerForm.name.trim();
  registerForm.surname = registerForm.surname.trim();
  registerForm.email = registerForm.email.trim();
  registerForm.password = registerForm.password.trim();


  if (registerForm.password.length < 8) {
    errors.value.push('Password must be at least 8 characters long');
    return;
  }

  axios.post(import.meta.env.VITE_API_URL + 'auth/signup', registerForm)
    .then((response: AxiosResponse) => {
      console.log(response.data);
      setAccessToken(response.data.access_token);
      currentUser$!.next({ email: registerForm.email });
      router.push('/');
    })
    .catch((e: AxiosError) => {
      errors.value.push(e.message);
    })

}

</script>

<template>
  <div class="register-container">
    <div class="register-card-border">
      <div class="register-card">
        <h1 class="title">Register</h1>
        <form @submit.prevent="register">
          <div v-if="errors.length" class="error-messages">
            <p v-for="error in errors" :key="error" class="error">
              {{ error }}
            </p>
          </div>

          <div class="form-group">
            <FloatLabel>
              <InputText id="name" v-model="registerForm.name" class="w-full" />
              <label for="name">Name</label>
            </FloatLabel>
          </div>

          <div class="form-group">
            <FloatLabel>
              <InputText id="surname" v-model="registerForm.surname" class="w-full" />
              <label for="surname">Surname</label>
            </FloatLabel>
          </div>

          <div class="form-group">
            <FloatLabel>
              <InputText id="email" v-model="registerForm.email" class="w-full" />
              <label for="email">Email</label>
            </FloatLabel>
          </div>

          <div class="form-group">
            <FloatLabel>
              <Password v-model="registerForm.password" inputId="password" class="w-full" toggleMask />
              <label for="password">Password</label>
            </FloatLabel>
          </div>

          <Button type="submit" :disabled="!isValid" label="Register" class="w-full" severity="primary" raised />
        </form>

        <RouterLink to="/login" class="login-link">Already registered? Sign in</RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-container {
  /* min-height: 100vh; */
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  margin-top: 3rem;
  /* background-color: #faf9f9; */
}

.register-card-border {
  padding: 3px;
  border-radius: 30px;
  width: 100%;
  max-width: 400px;
  background: linear-gradient(180deg, #00bd7e 10%, rgba(33, 150, 243, 0) 30%);

}

.register-card {
  background: white;
  padding: 2rem;
  border-radius: 30px;
  width: 100%;
}

.title {
  text-align: center;
  color: #333;
  margin-bottom: 1.5rem;
  font-size: 1.75rem;
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

.login-link {
  display: block;
  text-align: center;
  border-radius: 5px;
  margin-top: 1.5rem;
  color: #666;
  text-decoration: none;
  transition: color 0.2s;
}

.login-link:hover {
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

@media screen and (max-width: 768px) {
  .register-container {
    min-width: auto;
    padding: 0.5rem;
  }

  .register-card {
    padding: 1.5rem;
  }

  .title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }

  .form-group {
    margin-bottom: 1rem;
  }
}

@media screen and (max-width: 480px) {
  .register-card {
    padding: 1rem;
  }

  .title {
    font-size: 1.25rem;
  }
}
</style>