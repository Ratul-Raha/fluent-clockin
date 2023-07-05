<template>
    <el-dialog :visible="visible" @close="handleClose" title="Video Player Settings">
      <el-form label-width="120px">
        <el-form-item label="Autoplay">
          <el-radio-group v-model="autoplay">
            <el-radio label="yes">Yes</el-radio>
            <el-radio label="no">No</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Audio">
          <el-radio-group v-model="audio">
            <el-radio label="on">On</el-radio>
            <el-radio label="off">Off</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Player Size">
          <el-select v-model="playerSize" placeholder="Select Size">
            <el-option label="Small" value="small"></el-option>
            <el-option label="Medium" value="medium"></el-option>
            <el-option label="Large" value="large"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Video URL">
          <el-input v-model="videoUrl"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="handleClose">Cancel</el-button>
        <el-button type="primary" @click="saveSettings">Save</el-button>
      </span>
    </el-dialog>
  </template>
  
  <script>
  export default {
    props: {
      videoPlayer: {
        type: Object,
        required: true,
      },
      visible: {
        type: Boolean,
        required: true,
      },
    },
    data() {
      return {
        autoplay: '',
        audio: '',
        playerSize: '',
        videoUrl: '',
      };
    },
    mounted() {
      this.autoplay = this.videoPlayer.autoplay;
      this.audio = this.videoPlayer.audio;
      this.playerSize = this.videoPlayer.playerSize;
      this.videoUrl = this.videoPlayer.videoUrl;
    },
    methods: {
      handleClose() {
        this.autoplay = '';
        this.audio = '';
        this.playerSize = '';
        this.videoUrl = '';
        this.$emit('close');
      },
      saveSettings() {
        const updatedSettings = {
          autoplay: this.autoplay,
          audio: this.audio,
          playerSize: this.playerSize,
          videoUrl: this.videoUrl,
        };
        console.log('Updated Video Player Settings:', updatedSettings);
        this.handleClose();
      },
    },
  };
  </script>
  