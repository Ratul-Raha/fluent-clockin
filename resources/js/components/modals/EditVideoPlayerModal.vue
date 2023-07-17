<template>
  <div class="edit-video-player-modal">
    <div class="form-group">
      <span class="label">Title*:</span>
      <input
        v-model="editedVideoPlayer.title"
        type="text"
        maxlength="30"
        placeholder="Max 20 characters"
      />
    </div>
    <p v-if="!editedVideoPlayer.title" style="margin-left:100px" class="error-message">Please provide a title.</p>
    <div class="form-group">
      <span class="label">Description:</span>
      <el-input
        v-model="editedVideoPlayer.description"
        type="textarea"
        :rows="4"
        maxlength="100"
        placeholder="Max 100 characters"
      ></el-input>
    </div>

    <div class="form-group">
      <label class="label">Autoplay*:</label>
      <div class="form-field">
        <el-radio-group v-model="editedVideoPlayer.autoplay">
          <el-radio label="yes">Yes</el-radio>
          <el-radio label="no">No</el-radio>
        </el-radio-group>
      </div>
    </div>
    <div class="form-group">
      <label class="label">Audio*:</label>
      <div class="form-field">
        <el-radio-group v-model="editedVideoPlayer.audio">
          <el-radio label="on" :disabled="editedVideoPlayer.autoplay === 'yes'">On</el-radio>
          <el-radio label="off">Off</el-radio>
        </el-radio-group>
      </div>
    </div>
    <div class="form-group">
      <label class="label">Controls*:</label>
      <div class="form-field">
        <el-radio-group v-model="editedVideoPlayer.controls">
          <el-radio label="on">On</el-radio>
          <el-radio label="off">Off</el-radio>
        </el-radio-group>
      </div>
    </div>
    <div class="form-group">
      <label class="label">Player Size*:</label>
      <div class="form-field">
        <select
          v-model="editedVideoPlayer.player_size"
          placeholder="Select a size"
        >
          <option label="Small" value="small"></option>
          <option label="Medium" value="medium"></option>
          <option label="Large" value="large"></option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="label">Video URL*:</label>
      <div class="form-field">
        <input
          v-model="editedVideoPlayer.video_url"
          type="text"
          placeholder="youtube urls, urls with .mp4 extension"
        />
        <el-input v-model="editedVideoPlayer.id" type="hidden"></el-input>
      </div>
    </div>
    <el-alert title="" type="warning" show-icon
      >For youtube video policy, autoplay can't start with audio is
      on.</el-alert
    >
    <div class="modal-buttons">
      <el-button type="primary" @click="saveEditedVideoPlayer">Save</el-button>
      <el-button @click="cancelEditVideoPlayerForm">Cancel</el-button>
    </div>
  </div>
</template>

<script>
export default {
  props: ["editVideoPlayerModalVisible", "editedVideoPlayer"],
  methods: {
    cancelEditVideoPlayerForm() {
      this.$emit("cancelEditVideoPlayerForm");
    },
    saveEditedVideoPlayer() {
      this.$emit("saveEditedVideoPlayer", this.editedVideoPlayer);
    },
  },
  watch: {
    'editedVideoPlayer.autoplay': function (newVal) {
      if (newVal === 'yes') {
        this.editedVideoPlayer.audio = 'off';
      } else {
        // Otherwise, enable both audio options
        // If you have other conditions or logic, you can add them here
      }
    },
  },
};
</script>
<style scoped>
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

.edit-video-player-modal input[type="text"] {
  width: 100%;
  padding-left: 10px;
  border: 1px solid #cccccc;
  border-radius: 4px;
  font-size: 14px;
  box-sizing: border-box;
}
</style>
