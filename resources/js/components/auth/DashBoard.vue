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
        <el-menu-item index="" @click="openClockinModal">
          <i class="el-icon-time"></i>
          <span slot="title">Web ClockIn</span>
        </el-menu-item>
        <el-menu-item index="/web-clockout">
          <i class="el-icon-time"></i>
          <span slot="title">Web ClockOut</span>
        </el-menu-item>
        <el-menu-item index="" @click="openVideoPlayerModal">
          <i class="el-icon-media"></i>
          <span slot="title">Add Video Player</span>
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

    <!-- Clockin Modal -->
    <el-dialog
      title="ClockIn"
      v-model="clockinModalVisible"
      width="30%"
      @close="resetClockinForm"
    >
      <div class="clockin-modal">
        <div class="form-group">
          <span class="label">Date:</span>
          <span class="value">{{ currentDate }}</span>
        </div>
        <div class="form-group">
          <span class="label">Time:</span>
          <span class="value">{{ currentTime }}</span>
        </div>
        <div class="form-group">
          <span class="label">Location:</span>
          <span class="value">{{ currentLocation }}</span>
        </div>
        <div class="modal-buttons">
          <el-button type="primary" @click="confirmClockin">Confirm</el-button>
          <el-button @click="cancelClockin">Cancel</el-button>
        </div>
      </div>
    </el-dialog>
    <!-- Video Player Modal -->
    <el-dialog
      title="Add Video Player"
      v-model="videoPlayerModalVisible"
      width="30%"
      @close="cancelVideoPlayerForm"
    >
      <div class="video-player-modal">
        <div class="form-group">
          <span class="label">Title:</span>
          <el-input v-model="videoPlayerForm.title" type="text"></el-input>
        </div>
        <div class="form-group">
          <span class="label">Description:</span>
          <el-input
            v-model="videoPlayerForm.description"
            type="textarea"
            :rows="4"
          ></el-input>
        </div>
        <div class="modal-buttons">
          <el-button type="primary" @click="submitVideoPlayerForm"
            >Submit</el-button
          >
          <el-button @click="cancelVideoPlayerForm">Cancel</el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  ElAvatar,
  ElMenu,
  ElMenuItem,
  ElMain,
  ElTable,
  ElTableColumn,
  ElDialog,
  ElButton,
} from "element-plus";
import { RouterLink } from "vue-router";
import axios from "axios";

export default {
  components: {
    ElAvatar,
    ElMenu,
    ElMenuItem,
    ElMain,
    ElTable,
    ElTableColumn,
    ElDialog,
    ElButton,
    RouterLink,
  },
  data() {
    return {
      clockinList: [
        {
          serialNo: 1,
          date: "2023-06-01",
          time: "09:00 AM",
          location: "Office",
        },
        { serialNo: 2, date: "2023-06-02", time: "08:30 AM", location: "Home" },
        {
          serialNo: 3,
          date: "2023-06-03",
          time: "10:15 AM",
          location: "Client Site",
        },
      ],
      clockinModalVisible: false,
      videoPlayerModalVisible: false,
      videoPlayerForm: {
        title: "",
        description: "",
      },
      currentDate: "",
      currentTime: "",
      currentLocation: "",
      userProfileName: "Goutom Dash",
      avatarUrl: "path/to/avatar.png",
    };
  },
  methods: {
    openVideoPlayerModal() {
        console.log(clk_ajax.ajaxurl);
      this.videoPlayerForm.title = "";
      this.videoPlayerForm.description = "";
      this.videoPlayerModalVisible = true;
    },
    submitVideoPlayerForm() {
      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
            action: 'add_video_player',
            nonce: clk_ajax.clk_nonce,
            title: this.videoPlayerForm.title,
            description: this.videoPlayerForm.description,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", 'add_video_player');
        },
        success: function (response) {
          console.log("Form submission successful:", response);
        },
        error: function (error) {
          console.error("Form submission failed:", error);
        },
      });

      this.videoPlayerModalVisible = false;
    },

    cancelVideoPlayerForm() {
      this.videoPlayerModalVisible = false;
    },
    openClockinModal() {
      this.getClockinDateTime();
      this.getCurrentLocation();
      this.clockinModalVisible = true;
    },
    resetClockinForm() {
      this.currentDate = "";
      this.currentTime = "";
      this.currentLocation = "";
    },
    getClockinDateTime() {
      const now = new Date();
      console.log(now);
      this.currentDate = now.toLocaleDateString();
      this.currentTime = now.toLocaleTimeString();
    },
    async getCurrentLocation() {
      try {
        const response = await axios.get("https://ipinfo.io/json");
        const { city, region, country, loc } = response.data;
        const [latitude, longitude] = loc.split(",");
        this.currentLocation = `${city}, ${region}, ${country} (${latitude}, ${longitude})`;
      } catch (error) {
        console.error("Error retrieving location:", error);
      }
    },
    confirmClockin() {
      // Perform clock-in logic here
      console.log("ClockIn confirmed");
      this.clockinModalVisible = false;
    },
    cancelClockin() {
      this.clockinModalVisible = false;
    },
    logout() {
      // Perform logout logic here
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

.clockin-modal {
  margin-bottom: 20px;
}

.modal-buttons {
  text-align: right;
  margin-top: 20px;
}

.form-group {
  margin-bottom: 10px;
}

.label {
  font-weight: bold;
}

.value {
  margin-left: 10px;
}
</style>
