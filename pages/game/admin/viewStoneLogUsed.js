const app = getApp()
Page({

  data: {
    list: null
  },


  onLoad: function (options) {
    this.setData({
      list:JSON.parse(options.log)
    })
    
  },


})