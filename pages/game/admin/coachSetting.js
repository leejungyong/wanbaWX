var aid=null
const app=getApp()
var arr=[],event
Page({


  data: {
   coach:'',
    coachTelList: [],
   telephone:''
  },

  /**input框改变事件 */
  coachTel(e){
    event=e
    this.setData({
      telephone:e.detail.value
    })
  },

  /**添加电话号码到数组*/
  addTel(){
    let reg = /^1[34578]\d{9}$/
    let that=this
     if(!reg.test(that.data.telephone)){
      wx.showToast({
        title: '请输入正确的手机号！',
        icon: 'none'
      })
     } else if (that.data.coachTelList.indexOf(that.data.telephone)!=-1){
       wx.showToast({
         title: '该号码已存在！',
         icon: 'none'
       })
    }else{
       arr.push(that.data.telephone)
       that.setData({
         coachTelList:arr,
         telephone:''
       })
       event.detail.value=''
    }
    console.log(arr)
    console.log(that.data.telephone)
  },

  /**删除某一个号码*/
  deleteTel(e){
    let index=e.currentTarget.dataset.idx
    arr.splice(index,1)
    this.setData({
      coachTelList:arr
    })
  },

  /**提交数据 */
  commitTel(){
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postCoach',
      data: {
        aid: aid,
        coach: that.data.coachTelList.join(' ')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let pages = getCurrentPages()
        let prepage = pages[pages.length - 1]
        prepage.setData({
          coach: that.data.coachTelList.join(' ')
        })
        console.log(that.data.coachTelList.join(' '))
        wx.showToast({
          title: data.msg,
        })
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },

  onUnload:function(){
      this.commitTel()
  },
  onLoad: function (options) {
    let coach=options.coach
    aid = options.aid
    arr=coach.split(' ')
    console.log(coach)
    for(let i=0;i<arr.length;i++){
      arr[i].trim()
      if(arr[i]==''){
        arr.splice(i,1)
      }
    }
    console.log(arr)
    this.setData({
      coachTelList:arr
    })
    console.log(this.data.coachTelList)
  }

})