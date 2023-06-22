<template>
    <div class="dashboard">
      <el-aside width="225px">
        <div class="user-profile">
          <el-avatar :src="avatarUrl" size="large"></el-avatar>
          <div class="user-name">{{ userProfileName }}</div>
        </div>
        <el-menu default-active="/dashboard" router>
          <el-menu-item index="/dashboard">
            <i class="el-icon-house"></i>
            <span slot="title">Dashboard</span>
          </el-menu-item>
          <el-menu-item index="/web-clockin">
            <i class="el-icon-time"></i>
            <span slot="title">Web ClockIn</span>
          </el-menu-item>
          <el-menu-item index="/web-clockout">
            <i class="el-icon-time"></i>
            <span slot="title">Web ClockOut</span>
          </el-menu-item>
          <el-menu-item index="/logout" @click="logout">
            <i class="el-icon-switch-button"></i>
            <span slot="title">Log Out</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-main>
        <h2 class="page-title">ClockIn Time</h2>
        <el-table :data="clockinList" border>
          <el-table-column prop="serialNo" label="Serial No"></el-table-column>
          <el-table-column prop="date" label="Date"></el-table-column>
          <el-table-column prop="time" label="Time"></el-table-column>
          <el-table-column prop="location" label="Location"></el-table-column>
        </el-table>
      </el-main>
    </div>
  </template>
  
  <script>
  import { ElAvatar, ElMenu, ElMenuItem, ElMain, ElTable, ElTableColumn } from 'element-plus';
  import { RouterLink } from 'vue-router';
  
  export default {
    components: {
      ElAvatar,
      ElMenu,
      ElMenuItem,
      ElMain,
      ElTable,
      ElTableColumn,
      RouterLink,
    },
    data() {
      return {
        clockinList: [
          { serialNo: 1, date: '2023-06-01', time: '09:00 AM', location: 'Office' },
          { serialNo: 2, date: '2023-06-02', time: '08:30 AM', location: 'Home' },
          { serialNo: 3, date: '2023-06-03', time: '10:15 AM', location: 'Client Site' },
        ],
      };
    },
    computed: {
      userProfileName() {
        return 'Goutom Dash';
      },
      avatarUrl() {
        return 'path/to/avatar.png';
      },
    },
    methods: {
      logout() {
      },
    },
  };
  </script>
  
  <style scoped>
  .dashboard {
    display: flex;
    height: 100%;
  }
  
  .el-aside {
    background-color: #f3f3f3;
    margin-top: 20px;
  }
  
  .user-profile {
    display: flex;
    align-items: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #cccccc;
  }
  
  .user-name {
    font-weight: bold;
    margin-left: 10px;
  }
  
  .el-main {
    flex-grow: 1;
    padding: 20px;
  }
  
  .page-title {
    margin-bottom: 20px;
  }
  
  .el-table th,
  .el-table td {
    padding: 10px;
    border-bottom: 1px solid #cccccc;
  }
  </style>
  