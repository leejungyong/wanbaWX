const app = getApp()
Page({

  _data:{
    aid:0
  },
  data: {  
    swiper: {
      teams: null,
      indicatorDots: false,
      autoplay: false,
      interval: 5000,
      duration: 1000,
      current: 0
     
    },
   role:['我是队长','我是队员'],
   
    flag: true,
 desc:''
  },
  show(){
    this.setData({
      flag:false
    })
  },
  hide(){
    this.setData({
      flag:true
    })
  },
  isCaptain(){
    let current = this.data.swiper.current
    let teamid = this.data.swiper.teams[current].displayorder
    let teamname = this.data.swiper.teams[current].name
    let aid = this._data.aid
    let ops = {
      act: 'isCaptain',
      openid: wx.getStorageSync('openid'),
      teamid: teamid,
      teamname:teamname,
      aid: aid
    }
    //console.log(ops)
    wx.navigateTo({
      url: './showcode?ops=' + JSON.stringify(ops),
    })
  },
  isMember(){
    let current = this.data.swiper.current
    let teamid = this.data.swiper.teams[current].displayorder
    let aid = this._data.aid
    wx.showLoading({
      title: '数据请求中',
    })
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=joinTeam',
      data: {
        openid: wx.getStorageSync('openid'),
        roleid: 1,
        teamid: teamid,
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        // console.log(res)
        wx.hideLoading()
        wx.showToast({
          title: '加入成功',
          success: (res) => {
            wx.reLaunch({
              url: './main?aid=' + aid,
            })
          }
        })
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  bindPickerChange: function (e) {
    //console.log('picker发送选择改变，携带值为', e.detail.value)
    let roleid = e.detail.value
    let openid=wx.getStorageSync('openid')
    let current=this.data.swiper.current
    let teamid = this.data.swiper.teams[current].displayorder
    let aid=this._data.aid
    //console.log(aid)
    if(roleid==0){
      let ops = {
        act: 'isCaptain',
        openid: wx.getStorageSync('openid'),
        teamid: teamid,
        aid: this._data.aid
      }
       wx.navigateTo({
         url: './showcode?ops='+ JSON.stringify(ops),
       })
    }else{
      wx.showLoading({
        title: '数据请求中',
      })
      wx.request({
        url: app.globalData.config.apiUrl+'index.php?act=joinTeam',
        data: {
          openid:openid,
          roleid:roleid,
          teamid:teamid,
          aid:aid
        },
        method: 'POST',
        success: (res) => {
         // console.log(res)
          wx.hideLoading()
          wx.showToast({
            title: '加入成功',
            success:(res)=>{
              wx.reLaunch({
                url: './main?aid='+aid,
              })
            }
          })
        },
        fail: (err) => {
          wx.hideLoading()
          wx.showToast({
            title: '网络错误',
            icon: 'none'
          })
        }
      })
    }
    
  },


  bindchange: function(e) {
    let swiper = this.data.swiper
    swiper.current = e.detail.current
    let index = e.detail.current
    let desc = swiper.teams[index].desc
    //console.log(desc)
    this.setData({
      swiper: swiper,
      desc:desc
    })
   // console.log(this.data.swiper.current)
  },
  onLoad: function(options) {
    let aid = options.aid
    console.log(aid)
      this._data.aid=aid
      this.fetch(aid)
  },
  fetch(aid){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getTeamSetting',
      data: {
        
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let teamsetting = res.data.teamsetting
        wx.hideLoading()
        let swiper = that.data.swiper
        swiper.teams = teamsetting
        let desc = teamsetting[0].desc
        that.setData({
          swiper: swiper,
          desc:desc
        })
        
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  pre() {
    let swiper = this.data.swiper
    //let current = this.data.current>=1 ? this.data.current-1 :0;
    let current = swiper.current
    swiper.current = current > 0 ? current - 1 : swiper.teams.length - 1;
    this.setData({
      swiper: swiper
    })
   // console.log(current)

  },
  next() {
    let swiper = this.data.swiper
    let current = swiper.current
    // let current = this.data.current < this.data.imgUrls.length -1 ? this.data.current + 1 : this.data.imgUrls.length-1 ;
    swiper.current = current < (swiper.teams.length - 1) ? current + 1 : 0;
    this.setData({
      swiper: swiper,
    })
  //  console.log(current)
  }

})