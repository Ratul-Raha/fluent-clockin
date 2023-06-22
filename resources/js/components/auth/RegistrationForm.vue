<template>
    <div class="registration-page">
      <div class="registration-card">
        <h2 class="registration-title">Fluent Clokin Registration</h2>
        <div class="card-content">
          <form class="registration-form" @submit.prevent="register">
            <div class="form-field">
              <label for="username">Username</label>
              <input
                id="username"
                type="text"
                v-model.trim="form.username"
                :class="{ 'is-invalid': formErrors.username }"
                placeholder="Enter your username"
              />
              <div v-if="formErrors.username" class="error-message">{{ formErrors.username }}</div>
            </div>
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
            <div class="form-field">
              <label for="confirmPassword">Confirm Password</label>
              <input
                id="confirmPassword"
                type="password"
                v-model.trim="form.confirmPassword"
                :class="{ 'is-invalid': formErrors.confirmPassword }"
                placeholder="Confirm your password"
              />
              <div v-if="formErrors.confirmPassword" class="error-message">{{ formErrors.confirmPassword }}</div>
            </div>
            <div class="form-field form-actions">
              <button type="submit">Register</button>
            </div>
          </form>
          <div class="login-link">
          Already have an account? <router-link to="/login">Log in here</router-link>
        </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Register',
    data() {
      return {
        form: {
          username: '',
          email: '',
          password: '',
          confirmPassword: ''
        },
        formErrors: {}
      };
    },
    methods: {
      register() {
        this.formErrors = {};
  
        if (!this.validateForm()) {
          return;
        }
  
        // Proceed with registration logic
      },
      validateForm() {
        this.formErrors = {};
  
        if (!this.form.username) {
          this.formErrors.username = 'Username is required.';
        }
  
        if (!this.form.email) {
          this.formErrors.email = 'Email is required.';
        } else if (!this.isValidEmail(this.form.email)) {
          this.formErrors.email = 'Invalid email address.';
        }
  
        if (!this.form.password) {
          this.formErrors.password = 'Password is required.';
        }
  
        if (!this.form.confirmPassword) {
          this.formErrors.confirmPassword = 'Confirm Password is required.';
        } else if (this.form.confirmPassword !== this.form.password) {
          this.formErrors.confirmPassword = 'Passwords do not match.';
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
  .registration-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f3f3f3;
  }
  
  .registration-card {
    width: 400px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  
  .registration-title {
    text-align: center;
    font-size: 24px;
    margin: 20px 0;
    color: #333333;
  }
  
  .card-content {
    padding: 20px;
  }
  
  .registration-form .form-field {
    margin-bottom: 20px;
  }
  
  .registration-form label {
    display: block;
    font-weight: bold;
  }
  
  .registration-form input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .registration-form button {
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
  