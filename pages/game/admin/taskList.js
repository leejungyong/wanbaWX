const app = getApp()
let arr=[]
Page({ 
  data: {
    swiper: null,
    list: null,
    syslist:null,
    circular: true,
    indicatorDots: false,
    indicatorcolor: "#000",
    vertical: false,
    autoplay: true,
    interval: 2500,
    duration: 100,
    imgheights: [],
    imgwidth: 750,
    current: 0,
    imgwidth: 0,
    imgheight: 160,
    navbar: ['系统题库', '我的题库'],
    currentTab: 0,
    display:'none',
    openSide:false,
    typeList: [],
    choosetype:[],
    keyword:'',
    order:'0',
    imageList:[],
    page:0,
    pagesize:'10',
    syspages:null,
    mypages:null,
    init:true
  },
  stopLoadMoreTiem:false,
  //显示侧边栏
  showSide() {
    let that = this
    console.log(that.data.choosetype)

      this.setData({
        openSide: true,
        display: 'block'
      })

  },
  //关闭侧边栏
  hideSide() {
    this.setData({
      openSide: false,
      display: 'none',
    })
  },

  //选择类别
  chooseType(e) {
    let that = this
    let index = e.target.dataset.idx
    let type = that.data.typeList[index].name
    let typearr = that.data.typeList
    typearr[index].checked = !typearr[index].checked
    if (typearr[index].checked) {
      if (arr.indexOf(type) == -1) {
        arr.push(type)
      }
    } else {
      arr.splice(arr.indexOf(type), 1)
    }
    this.setData({
      choosetype: arr,
      typeList: typearr
    })
    console.log(that.data.choosetype)
  },

  //关键词
  changeKeyWord(e) {
    console.log(e.detail.value)
    this.setData({
      keyword: e.detail.value
    })
  },


  //重置按钮
  resetButton() {
    let typearr = this.data.typeList
    typearr.map(item => {
      item.checked = false
    })
    arr = []
    this.setData({
      choosetype: [],
      keyword: '',
      typeList: typearr
    })
  },
  //确定按钮
  sureButton() {
    let that = this
    that.setData({
      page:0,
      syslist:[],
      list:[],
      display: 'none'
    })
    that.fetch()
    // that.setData({
    //   keyword:'',
    //   choosetype:[]
    // })
    // arr=[]
  },
 
 //图片预览
  previewImage(e){
    let that=this
    let index=e.target.dataset.idx
    let pics=that.data.currentTab==0?that.data.syslist[index].pics:that.data.list[index].pics
    let picsarr=pics.map(item=>{
      return item.url
    })
    console.log(picsarr)
    that.setData({
      imageList:picsarr
    })
    var current=e.target.dataset.src
    wx.previewImage({
      current:current,
      urls:that.data.imageList ,
    })
  },
  useSysQuestion(e) {
    let id = e.currentTarget.id
    let ops = this.data.syslist[id]
    // console.log(ops)
    // let pages = getCurrentPages()
    // let prepage = pages[pages.length - 2]
    // let poiInfo = prepage.data.poiInfo
    // poiInfo.memo = ops.memo
    // poiInfo.answer = ops.answer
    // poiInfo.qtype = ops.qtype
    // poiInfo.pics = ops.pics
    // poiInfo.media = ops.media
    // poiInfo.url = ops.url
    // console.log(poiInfo)
    // prepage.setData({
    //   poiInfo: poiInfo
    // })
    // wx.navigateBack()

    wx.navigateTo({
      url: './taskDetail?ops=' + JSON.stringify(ops),
    })
  },

  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    if (url.indexOf('/pages/') > -1) {
      wx.navigateTo({
        url: url
      })
    } else {
      wx.navigateTo({
        url: '/pages/index/showWx?url=' + url
      })
    }
  },
  useQuestion(e) {
    let id = e.currentTarget.id
    let ops = this.data.list[id]
    // console.log(ops)
    // let pages=getCurrentPages()
    // let prepage=pages[pages.length-2]
    // let poiInfo=prepage.data.poiInfo
    // poiInfo.memo=ops.memo
    // poiInfo.answer=ops.answer
    // poiInfo.qtype=ops.qtype
    // poiInfo.pics=ops.pics
    // poiInfo.media = ops.media
    // poiInfo.url = ops.url
    // console.log(poiInfo)
    // prepage.setData({
    //   poiInfo:poiInfo
    // })
    // wx.navigateBack()
    
    wx.navigateTo({
      url: './taskDetail?ops=' + JSON.stringify(ops),
    })
  },
  imageLoad: function (e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    //把每一张图片的对应的高度记录到数组里  
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },



  bindchange: function (e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },
  navbarTap: function (e) {
    
    this.resetButton() 
    this.setData({
      currentTab: e.currentTarget.dataset.idx,
      order:e.currentTarget.dataset.idx,
      choosetype:[],
      keyword:'',
      list:[],
      syslist:[],
      page:0
    })
    this.fetch()
    
  },
  fetch() {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    console.log(that.data.page)
    that.stopLoadMoreTiem=true
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myQuestionData',
      data: {
        openid: wx.getStorageSync('openid'),
        token:'',
        cat:that.data.choosetype,
        key:that.data.keyword,
        order:that.data.order,
        currentpage:that.data.page,
        pagesize:that.data.pagesize
      },
      method: 'POST',
      success: (res) => {
        console.log(res.data)
        that.stopLoadMoreTiem=false
        let cats=res.data.cats.map(item=>{
          return {name:item,checked:false}
        })
        let data = res.data
        let syspagenum=Math.ceil(data.systotal/that.data.pagesize)
        let mypagenum=Math.ceil(data.mytotal/that.data.pagesize)
        if(that.data.init){
          that.setData({
            swiper: data.swiper,
            list: data.list,
            syslist: data.syslist,
            typeList: cats,
            syspages:syspagenum,
            mypages:mypagenum,
            init:false
          })
        }else{
          if(that.data.currentTab==0){
            let newSysList = that.data.syslist.concat(data.syslist)
            that.setData({
              syslist: newSysList,
              syspages: syspagenum,
            })
          }else{
            let newList = that.data.list.concat(data.list)
            that.setData({
              list: newList,
              mypages: mypagenum,
            })
          }
        }
        console.log(that.data.syslist)
        wx.hideLoading()



      },
      fail: (err) => {
        wx.hideLoading()
        that.stopLoadMoreTiem=false
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onLoad: function (options) {
    this.fetch()
    
  },

  /**
* 页面上拉触底事件的处理函数
*/
  onReachBottom: function () {
    let that = this
    if(that.data.currentTab==0){
      if (!that.stopLoadMoreTiem&&(that.data.page<that.data.syspages-1)) {
        this.setData({
          page: that.data.page + 1
        })
        this.fetch()
      }
    } else if (that.data.currentTab==1){
      if (!that.stopLoadMoreTiem && (that.data.page < that.data.mypages-1)) {
        this.setData({
          page: that.data.page + 1
        })
        this.fetch()
      }
    }

  },
 

})