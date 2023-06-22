<template>
    <div class="login-page">
      <div class="login-card">
        <h2 class="login-title">Fluent Clokin Login</h2>
        <div class="card-content">
          <form class="login-form" @submit.prevent="login">
            <div class="form-field">
              <label for="email">Email</label>
              <input
                id="email"
                type="email"
                v-model.trim="form.email"
                :class="{ 'is-invalid': formErrors.email }"
                placeholder="Enter your email"
              />
              <div v-if="formErrors.email" class="error-message">{{ formErrors.email }}</div>
            </div>
            <div class="form-field">
              <label for="password">Password</label>
              <input
                id="password"
                type="password"
                v-model.trim="form.password"
                :class="{ 'is-invalid': formErrors.password }"
                placeholder="Enter your password"
              />
              <div v-if="formErrors.password" class="error-message">{{ formErrors.password }}</div>
            </div>
            <div class="form-field form-actions">
              <button type="submit">Login</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          email: '',
          password: ''
        },
        formErrors: {}
      };
    },
    methods: {
      login() {
        this.formErrors = {};
  
        if (!this.validateForm()) {
          return;
        }
      },
      validateForm() {
        this.formErrors = {};
  
        if (!this.form.email) {
          this.formErrors.email = 'Email is required.';
        } else if (!this.isValidEmail(this.form.email)) {
          this.formErrors.email = 'Invalid email address.';
        }
  
        if (!this.form.password) {
          this.formErrors.password = 'Password is required.';
        }
  
        return Object.keys(this.formErrors).length === 0;
      },
      isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      }
    }
  };
  </script>
  
  <style scoped>
  .login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f3f3f3;
  }
  
  .login-card {
    width: 400px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  
  .login-title {
    text-align: center;
    font-size: 24px;
    margin: 20px 0;
    color: #333333;
  }
  
  .card-content {
    padding: 20px;
  }
  
  .login-form .form-field {
    margin-bottom: 20px;
  }
  
  .login-form label {
    display: block;
    font-weight: bold;
  }
  
  .login-form input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .login-form button {
    width: 100%;
    padding: 10px 20px;
    background-color: #3366FF;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .error-message {
    color: red;
    font-size: 14px;
    margin-top: 5px;
  }
  </style>
  