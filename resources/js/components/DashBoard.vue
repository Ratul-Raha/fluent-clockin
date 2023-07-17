<template>
  <div class="dashboard">
    <el-aside width="225px">
      <div class="user-profile">
        <el-avatar :src="avatarUrl" size="large"></el-avatar>
        <div class="user-name">{{ userProfileName }}</div>
      </div>
      <el-menu default-active="/" router>
        <el-menu-item index="/">
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
      <div v-if="displayedVideoPlayers.length > 0">
        <el-table
          :data="displayedVideoPlayers"
          v-if="displayedVideoPlayers.length > 0"
          border
        >
          <el-table-column prop="ID" label="ID" sortable width="100">
          </el-table-column>
          <el-table-column prop="post_title" label="Title" sortable>
          </el-table-column>
          <el-table-column prop="post_content" label="Description" sortable>
          </el-table-column>
          <el-table-column prop="shortcode" label="Short Code" sortable>
            <template #default="scope">
              [video_player id = "{{ scope.row.ID }}"]
              <el-icon
                @click="
                  copyShortcode('[video_player id= ' + scope.row.ID + ']')
                "
                ><CopyDocument
              /></el-icon>
            </template>
          </el-table-column>
          <el-table-column label="Actions" prop="ID">
            <template #default="scope">
              <el-button type="primary" @click="editVideoPlayer(scope.row)"
                ><el-icon><Edit /></el-icon
              ></el-button>
              <el-button type="danger" @click="deleteVideoPlayer(scope.row)"
                ><el-icon><Delete /></el-icon
              ></el-button>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          @size-change="handlePaginationSizeChange"
          @current-change="handlePaginationCurrentChange"
          :current-page="currentPage"
          :page-size="pageSize"
          :total="totalRows"
          layout="prev, pager, next"
          style="align-items: stretch"
        />
      </div>
      <el-alert v-else title="No data" show-icon
        >No video player available. Add from the sidemenu.</el-alert
      >
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
          <span class="label">Title*:</span>
          <input
            v-model="videoPlayerForm.title"
            type="text"
            maxlength="20"
            placeholder="Max 20 characters"
          />
          <p v-if="!videoPlayerForm.title" class="error-message">
            Please provide a title.
          </p>
        </div>
        <div class="form-group">
          <span class="label">Description:</span>
          <el-input
            v-model="videoPlayerForm.description"
            type="textarea"
            :rows="4"
            maxlength="100"
            placeholder="Max 100 characters"
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
    <!--Edit Video Player Setting Modal-->
    <el-dialog
      title="Edit Video Player Setting"
      v-model="editVideoPlayerModalVisible"
      width="30%"
      @close="cancelEditVideoPlayerForm"
    >
      <EditSettingModal
        :editedVideoPlayer="editedVideoPlayer"
        :editVideoPlayerModalVisible="editVideoPlayerModalVisible"
        @cancelEditVideoPlayerForm="cancelEditVideoPlayerForm"
        @saveEditedVideoPlayer="saveEditedVideoPlayer"
      ></EditSettingModal>
    </el-dialog>
  </div>
</template>

<script>
import { ElNotification } from "element-plus";
import EditSettingModal from "./modals/EditVideoPlayerModal.vue";

export default {
  components: {
    ElNotification,
    EditSettingModal,
  },
  data() {
    return {
      currentPage: 1,
      pageSize: 5,
      displayedVideoPlayers: [],
      totalRows: 0,
      videoPlayerModalVisible: false,
      videoPlayersListModalVisible: false,
      videoPlayerForm: {
        title: null,
        description: null,
      },
      editVideoPlayerModalVisible: false,
      editedVideoPlayer: {
        id: null,
        autoplay: "",
        audio: "",
        player_size: "",
        video_url: "",
        controls: "",
      },
      videoPlayersList: [],
      userProfileName: "Goutom Dash",
      avatarUrl: "",
    };
  },
  mounted() {
    this.fetchVideoPlayers();
  },

  methods: {
    handlePaginationSizeChange(newSize) {
      this.pageSize = newSize;
      this.fetchVideoPlayers();
    },
    handlePaginationCurrentChange(newPage) {
      this.currentPage = newPage;
      this.fetchVideoPlayers();
    },
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
          action: "clk_add_video_player",
          nonce: clk_ajax.clk_nonce,
          title: this.videoPlayerForm.title,
          description: this.videoPlayerForm.description,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "clk_add_video_player");
        },
        success: (response) => {
          if (response.success === true) {
            ElNotification({
              title: "Success!",
              message: response.message,
              duration: 4000,
              type: "success",
              offset: 20,
              customClass: "left-aligned-notification",
            });
            this.fetchVideoPlayers();
            this.editVideoPlayerModalVisible = true;
            this.editedVideoPlayer.title = response.title;
            this.editedVideoPlayer.description = response.description;
            this.editedVideoPlayer.player_size = "small";
            this.editedVideoPlayer.id = response.id;
            this.videoPlayerModalVisible = false;
          } else {
            ElNotification({
              title: "Error!",
              message: response.data,
              type: "error",
              offset: 20,
              customClass: "left-aligned-notification",
            });
          }
        },
        error: function (error) {
          console.error("Form submission failed:", error);
        },
      });
    },

    cancelVideoPlayerForm() {
      this.videoPlayerModalVisible = false;
    },

    fetchVideoPlayers() {
      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "clk_fetch_video_player",
          nonce: clk_ajax.clk_nonce,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "clk_fetch_video_player");
        },
        success: (response) => {
          if (response.data === "No data") {
            this.displayedVideoPlayers = [];
          } else {
            const videoPlayers = response.data;
            this.totalRows = videoPlayers.length;
            const start = (this.currentPage - 1) * this.pageSize;
            const end = start + this.pageSize;
            this.displayedVideoPlayers = videoPlayers.slice(start, end);
          }
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
          action: "clk_fetch_video_player_setting",
          nonce: clk_ajax.clk_nonce,
          id: row.ID,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "clk_fetch_video_player_setting");
        },
        success: (response) => {
          this.editedVideoPlayer.title = response.title;
          this.editedVideoPlayer.description = response.description;
          this.editedVideoPlayer.autoplay = response.autoplay;
          this.editedVideoPlayer.audio = response.audio;
          this.editedVideoPlayer.video_url = response.video_url;
          this.editedVideoPlayer.player_size = response.player_size;
          this.editedVideoPlayer.controls = response.controls;
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
      const requiredFields = [
        "title",
        "video_url",
        "autoplay",
        "audio",
        "player_size",
        "controls",
      ];
      const isFieldsValid = requiredFields.every((field) => {
        const fieldValue = this.editedVideoPlayer[field];
        return fieldValue && fieldValue.trim() !== "";
      });

      if (!isFieldsValid) {
        ElNotification({
          title: "Error",
          message: "Please fill in all the required fields.",
          type: "error",
          offset: 20,
        });
        return;
      }

      jQuery.ajax({
        url: clk_ajax.ajaxurl,
        type: "POST",
        data: {
          action: "clk_save_video_player_setting",
          nonce: clk_ajax.clk_nonce,
          id: this.editedVideoPlayer.id,
          autoplay: this.editedVideoPlayer.autoplay,
          audio: this.editedVideoPlayer.audio,
          player_size: this.editedVideoPlayer.player_size,
          video_url: this.editedVideoPlayer.video_url,
          title: this.editedVideoPlayer.title,
          description: this.editedVideoPlayer.description,
          controls: this.editedVideoPlayer.controls,
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-Action", "clk_save_video_player_setting");
        },
        success: (response) => {
          if (response.success === true) {
            ElNotification({
              title: "Success!",
              message: "Video Player Settings are saved!",
              duration: 2000,
              type: "success",
              offset: 20,
            });
            this.fetchVideoPlayers();
            this.editVideoPlayerModalVisible = false;
            this.editedVideoPlayer = {};
          } else {
            ElNotification({
              title: "Error!",
              message: response.data,
              duration: 4000,
              type: "error",
              offset: 20,
            });
          }
        },
        error: function (error) {
          ElNotification({
            title: "Error",
            message: error,
            type: "error",
            offset: 20,
          });
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
            action: "clk_delete_video_player",
            nonce: clk_ajax.clk_nonce,
            id: row.ID,
          },
          beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Action", "delete_video_player");
          },
          success: (response) => {
            ElNotification({
              title: "Success!",
              message: "Video Player deleted successfully",
              type: "success",
              duration: 4000,
              offset: 20,
            });
            this.fetchVideoPlayers();
            if (this.displayedVideoPlayers.length === 1) {
              if (this.currentPage > 1) {
                this.currentPage--;
              }
            }
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
      this.$message({
        message: "Shortcode copied to clipboard",
        type: "success",
        offset: 50,
      });
    },
  },
};
</script>

<style>
.left-aligned-notification .el-notification__content {
  text-align: left;
}
</style>

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

.video-player-modal input[type="text"] {
  width: 100%;
  padding-left: 10px;
  border: 1px solid #cccccc;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}

.edit-video-player-modal input[type="text"] {
  width: 100%;
  padding-left: 10px;
  border: 1px solid #cccccc;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}
</style>
