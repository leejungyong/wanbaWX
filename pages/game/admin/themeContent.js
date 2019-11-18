const app = getApp()
var aid=null
Page({

  /**
   * 页面的初始数据
   */
  data: {
    themeid: null,
    list: null,
    img:'',
    stamp:'',
    teamNum:'',
    actmode:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    aid=options.aid
    this.setData({
      themeid: options.themeid
    })
    this.fetch()
  },
  toAddTeam(){
    let displayorder=parseInt(this.data.list[this.data.list.length-1].displayorder)+1
    console.log(displayorder)
    wx.navigateTo({
      url: './newTeam?aid='+aid+'&themeid='+this.data.themeid+'&displayorder='+displayorder+'&teamnum='+this.data.teamNum,
    })
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getDefinedTeamById',
      method: 'POST',
      data: {
        aid:aid
      },
      success: res => {
        console.log(res.data)
        that.setData({
          list: res.data.list,
          teamNum:res.data.teamNum,
          actmode:parseInt(res.data.actmode)
        })


      }
    })
  },

  toEdit(e) {

    let index = e.currentTarget.dataset.idx
    let obj = this.data.list[index]

    obj.desc = obj.desc.replace('?', '？')
    obj.desc = obj.desc.replace('&', '')

    obj.pic=obj.pic.replace(/\?/,'？')
    wx.navigateTo({
      url: './teamEdit?index='+index+'&aid='+aid+'&actmode='+this.data.actmode+'&teamObj=' + JSON.stringify(obj),
    })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
      let that=this
    for(let i=0;i<that.data.list.length;i++){
      that.data.list[i]=i+1
    }
    console.log(that.data.list)

    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=',
      method:'POST',
      data:{

      },

    })
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})