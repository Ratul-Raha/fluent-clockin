<template>
  <div class="dashboard">
    <el-aside width="225px">
      <div class="user-profile">
        <el-avatar :src="avatarUrl" size="large"></el-avatar>
        <div class="user-name">{{ userProfileName }}</div>
      </div>
      <el-menu default-active="/dashboard" router>
        <el-menu-item index="/dashboard">
          <el-icon>
            <House />
          </el-icon>
          <span slot="title">Dashboard</span>
        </el-menu-item>
        <el-menu-item index="" @click="openVideoPlayerModal">
          <el-icon>
            <Plus />
          </el-icon>
          <span slot="title">Add Video Player</span>
        </el-menu-item>
      </el-menu>
    </el-aside>
    <el-main>
      <h2 class="page-title">Video Players setting</h2>
      <!-- Render the video players list here -->
      <el-table :data="videoPlayersList" border>
        <el-table-column prop="id" label="ID">
          <template #default="scope">
            {{ scope.row.ID }}
          </template>
        </el-table-column>
        <el-table-column prop="title" label="Title">
          <template #default="scope">
            {{ scope.row.post_title }}
          </template>
        </el-table-column>
        <el-table-column prop="description" label="Description">
          <template #default="scope">
            {{ scope.row.post_content }}
          </template>
        </el-table-column>
        <el-table-column prop="shortcode" label="Short Code">
          <template #default="scope">
            <div>
              {{ scope.row.shortcode }}
              <el-icon @click="copyShortcode(scope.row.shortcode)"
                ><CopyDocument
              /></el-icon>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="Actions">
          <template #default="scope">
            <el-button type="primary" @click="editVideoPlayer(scope.row)"
              ><el-icon> <Edit /> </el-icon
            ></el-button>
            <el-button type="danger" @click="deleteVideoPlayer(scope.row)">
              <el-icon> <Delete /> </el-icon
            ></el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-main>

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

    <!--EditPlayerModal-->
    <el-dialog
      title="Edit Video Player Setting"
      v-model="editVideoPlayerModalVisible"
      width="30%"
      @close="cancelEditVideoPlayerForm"
    >
      <div class="edit-video-player-modal">
        <div class="form-group">
          <label class="label">Autoplay:</label>
          <div class="form-field">
            <el-radio-group v-model="editedVideoPlayer.autoplay">
              <el-radio label="yes">Yes</el-radio>
              <el-radio label="no">No</el-radio>
            </el-radio-group>
          </div>
        </div>
        <div class="form-group">
          <label class="label">Audio:</label>
          <div class="form-field">
            <el-radio-group v-model="editedVideoPlayer.audio">
              <el-radio label="on">On</el-radio>
              <el-radio label="off">Off</el-radio>
            </el-radio-group>
          </div>
        </div>
        <div class="form-group">
          <label class="label">Player Size:</label>
          <div class="form-field">
            <el-select
              v-model="editedVideoPlayer.player_size"
              placeholder="Select size"
            >
              <el-option label="Small" value="small"></el-option>
              <el-option label="Medium" value="medium"></el-option>
              <el-option label="Large" value="large"></el-option>
            </el-select>
          </div>
        </div>
        <div class="form-group">
          <label class="label">Video URL:</label>
          <div class="form-field">
            <el-input
              v-model="editedVideoPlayer.video_url"
              type="text"
            ></el-input>
            <el-input v-model="editedVideoPlayer.id" type="hidden"></el-input>
          </div>
        </div>
        <div class="modal-buttons">
          <el-button type="primary" @click="saveEditedVideoPlayer"
            >Save</el-button
          >
          <el-button @click="cancelEditVideoPlayerForm">Cancel</el-button>
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
  ElRadio,
  ElRadioGroup,
  ElSelect,
  ElOption,
  ElInput,
  ElButton,
  ElNotification,
  ElContainer,
} from "element-plus";
import { RouterLink } from "vue-router";

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
    ElNotification,
    ElContainer,
    ElRadio,
    ElRadioGroup,
    ElSelect,
    ElOption,
    ElInput,
  },
  data() {
    return {
      videoPlayerModalVisible: false,
      videoPlayersListModalVisible: false,
      videoPlayerForm: {
        title: "",
        description: "",
      },
      editVideoPlayerModalVisible: false,
      editedVideoPlayer: {
        id: null,
        autoplay: "",
        audio: "",
        player_size: "",
        video_url: "",
      },
      videoPlayersList: [],
      userProfileName: "Goutom Dash",
      avatarUrl: "path/to/avatar.png",
    };
  },
  mounted() {
    this.fetchVideoPlayers();
  },

  methods: {
    openVideoPlayerModal() {
      this.videoPlayerForm.title = "";
      this.videoPlayerForm.description = "";
      this.videoPlayerModalVisible = true;
    },
    submitVideoPlayerForm() {
      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "add_video_player",
          nonce: clk_ajax.clk_nonce,
          title: this.videoPlayerForm.title,
          description: this.videoPlayerForm.description,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "add_video_player");
        },
        success: function (response) {
          ElNotification({
            title: "Prompt",
            message: "Video Player added successfully",
            duration: 0,
          });
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
    fetchVideoPlayers() {
      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "fetch_video_player",
          nonce: clk_ajax.clk_nonce,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "fetch_video_player");
        },
        success: (response) => {
          const videoPlayers = response.data;
          this.videoPlayersList = videoPlayers; // 'this' will refer to the Vue component instance
        },
        error: function (error) {
          console.error("Data fetching failed:", error);
        },
      });
    },
    editVideoPlayer(row) {
      this.editVideoPlayerModalVisible = true;
      this.editedVideoPlayer.id = row.ID;

      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "fetch_video_player_setting",
          nonce: clk_ajax.clk_nonce,
          id: row.ID,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "save_video_player_setting");
        },
        success: (response) => {
          const videoPlayerSetting = response.data;
          this.editedVideoPlayer.autoplay = videoPlayerSetting.autoplay[0];
          this.editedVideoPlayer.audio = videoPlayerSetting.audio[0];
          this.editedVideoPlayer.video_url = videoPlayerSetting.video_url[0];
          this.editedVideoPlayer.player_size =
            videoPlayerSetting.player_size[0];
        },
        error: function (error) {
          console.error("Data fetching failed:", error);
        },
      });
    },
    cancelEditVideoPlayerForm() {
      this.editVideoPlayerModalVisible = false;
    },
    saveEditedVideoPlayer() {
      console.log(this.editedVideoPlayer.player_size);
      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "save_video_player_setting",
          nonce: clk_ajax.clk_nonce,
          id: this.editedVideoPlayer.id,
          autoplay: this.editedVideoPlayer.autoplay,
          audio: this.editedVideoPlayer.audio,
          player_size: this.editedVideoPlayer.player_size,
          video_url: this.editedVideoPlayer.video_url,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "save_video_player_setting");
        },
        success: (response) => {
          const videoPlayers = response.data;
          this.videoPlayersList = videoPlayers;
        },
        error: function (error) {
          console.error("Data fetching failed:", error);
        },
      });
    },
    deleteVideoPlayer(row) {
      if (confirm("Are you sure you want to delete this video player?")) {
        jQuery.ajax({
          url: clk_ajax.ajaxurl,
          type: "POST",
          data: {
            action: "delete_video_player",
            nonce: clk_ajax.clk_nonce,
            id: row.ID,
          },
          beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Action", "delete_video_player");
          },
          success: (response) => {
            ElNotification({
              title: "Toast!",
              message: "Video Player deleted successfully",
              duration: 0,
            });
            this.fetchVideoPlayers();
          },
          error: function (error) {
            console.error("Deleting failed:", error);
          },
        });
      }
    },
    copyShortcode(shortcode) {
      const el = document.createElement("textarea");
      el.value = shortcode;
      document.body.appendChild(el);
      el.select();
      document.execCommand("copy");
      document.body.removeChild(el);
      this.$message.success("Shortcode copied to clipboard");
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

.edit-video-player-modal .form-group {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.edit-video-player-modal .form-group .label {
  width: 120px;
  margin-right: 10px;
  display: flex;
  align-items: center;
}

.edit-video-player-modal .form-field {
  flex: 1;
}

.modal-buttons {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

</style>
