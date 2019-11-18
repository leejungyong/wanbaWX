import { md5 } from '../../../utils/md5.js'
import { createTimeStamp } from '../../../utils/util.js'
import regeneratorRuntime from '../../../utils/runtime.js'
import {wxRequest}  from '../../../utils/wxrequest.js'
const app=getApp()
var token
Page({

 
  data: {
   flag:true
  },
  copyUrl(){

    let that=this
    let uid = md5(wx.getStorageSync('openid'))
    
    token = createTimeStamp()
     
     
      wx.setStorageSync('pc_login_token', token)
     let url = "http://www.wondfun.com/wanba/admin/#/login?uid=" + uid + '&token=' + token
      wx.setClipboardData({
        data: url,
        success(res) {

          wx.showModal({
            title: '提示',
            showCancel: false,
            content: '网址已复制到剪贴板,请在电脑端打开扫码登录',
            success(res) {
              that.setData({
                flag: false
              })
            }
          })
        }
      })
  },
  scan() {
    let that = this
    wx.scanCode({
      onlyFromCamera: false,
      success: (res) => {
        wx.removeStorageSync('pc_login_token')
        that.setData({
          flag: true
        })
        let result = res.result
       
        let arr=result.split('?')

        let s=arr[1]
        //console.log(s)
        if(s){
          let a=s.split('&')
          console.log(a)
          if(a && a.length==3){
             let a1=a[1]
             let a2=a[2]
            let u = a1.split('=')[1]
            console.log(u)
            let t = a2.split('=')[1]
            console.log(t)
            let uid = md5(wx.getStorageSync('openid'))
            if(u==uid && t==token){
              let url = 'https://www.wondfun.com/wanba/api/index.php?uid='+uid + '&act=pcWxLogin'+'&token='+t
              that.pcLogin(url)
                .then((ret) => {
                  
                  wx.showToast({
                    title: ret.msg,
                  })
                })
                .catch((err) => {
                  console.log(err)
                })
            }
          }
        }else{
          wx.showToast({
            title: '扫描失败，请复制正确网址到电脑端',
            icon:'none'
          })
        }
       

      },
      fail: (res) => {
        console.log(res)
      }
    })
  },

  async pcLogin(url){
    return  await  wxRequest(
      url,
      {
        data:{

        }
      }
    )
  },
  onLoad: function (options) {
   let pc_login_token=wx.getStorageSync('pc_login_token')
   if(pc_login_token){
     let now =createTimeStamp()
     if (now - pc_login_token<300){
         this.setData({
           flag:false
         })
     }else{
       this.setData({
         flag: true
       })
     }
   }else{
    

   }
  },


})