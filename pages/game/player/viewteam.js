const app = getApp()

Page({
  _data:{

  },
  data: {
    role:null,
    list:null,
    imgUrl:app.globalData.config.imgUrl
  },

  onLoad: function (options) {
    let ops=JSON.parse(options.ops)
   let  role=options.role
    let that=this
   console.log(role)
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=viewteam',
      data: {
        aid: ops.aid,
        teamid: ops.teamid
      },
      method: 'POST',
      success: (res) => {
         console.log(res)
         let data=res.data
         if(data){
           that.setData({
             list:data.teams,
             role:role
           })
           
         }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  getRidOfSomeone(openid,id){
    //console.log(openid)
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getRideOfSomeone',
      data: {
        openid:openid
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        if (data.status) {
          let list=that.data.list
          list.splice(id, 1)
          that.setData({
            list: list
          })
          that.setData({
            list: list
          })

        }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })

  },
  kickoff(e){
    let that=this
    let id = e.currentTarget.id
    let openid = that.data.list[id].openid
    if(openid!=wx.getStorageSync('openid')){
    wx.showModal({
      title: '警告',
      content: '确定要踢出该队员吗？',
      success(res) {
        if (res.confirm) {
          that.getRidOfSomeone(openid,id)
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })}

  }

})