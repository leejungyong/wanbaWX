import * as echarts from '../../../ec-canvas/echarts';
import Poster from '../../../poster/poster/poster';

const posterConfig = {
  jdConfig: {
    width: 750,
    height: 1334,
    backgroundColor: '#fff',
    debug: false,
    pixelRatio: 1,
    blocks: [
      {
        width: 690,
        height: 808,
        x: 30,
        y: 183,
        borderWidth: 2,
        borderColor: '#f0c2a0',
        borderRadius: 20,
      },
      {
        width: 634,
        height: 74,
        x: 59,
        y: 770,
        backgroundColor: '#fff',
        opacity: 0.5,
        zIndex: 100,
      },
    ],
    texts: [
      {
        x: 113,
        y: 61,
        baseLine: 'middle',
        text: '伟仔',
        fontSize: 32,
        color: '#8d8d8d',
      },
      {
        x: 30,
        y: 113,
        baseLine: 'top',
        text: '发现一个好物，推荐给你呀',
        fontSize: 38,
        color: '#080808',
      },
      {
        x: 92,
        y: 810,
        fontSize: 38,
        baseLine: 'middle',
        text: '标题标题标题标题标题标题标题标题标题',
        width: 570,
        lineNum: 1,
        color: '#8d8d8d',
        zIndex: 200,
      },
      {
        x: 59,
        y: 895,
        baseLine: 'middle',
        text: [
          {
            text: '2人拼',
            fontSize: 28,
            color: '#ec1731',
          },
          {
            text: '¥99',
            fontSize: 36,
            color: '#ec1731',
            marginLeft: 30,
          }
        ]
      },
      {
        x: 522,
        y: 895,
        baseLine: 'middle',
        text: '已拼2件',
        fontSize: 28,
        color: '#929292',
      },
      {
        x: 59,
        y: 945,
        baseLine: 'middle',
        text: [
          {
            text: '商家发货&售后',
            fontSize: 28,
            color: '#929292',
          },
          {
            text: '七天退货',
            fontSize: 28,
            color: '#929292',
            marginLeft: 50,
          },
          {
            text: '运费险',
            fontSize: 28,
            color: '#929292',
            marginLeft: 50,
          },
        ]
      },
      {
        x: 360,
        y: 1065,
        baseLine: 'top',
        text: '长按识别小程序码',
        fontSize: 38,
        color: '#080808',
      },
      {
        x: 360,
        y: 1123,
        baseLine: 'top',
        text: '超值好货一起拼',
        fontSize: 28,
        color: '#929292',
      },
    ],
    images: [
      {
        width: 62,
        height: 62,
        x: 30,
        y: 30,
        borderRadius: 62,
        url: 'https://www.wondfun.com/wanba/img/gamepic/wanba_qrcode.jpg',
      }
    ]

  },
  demoConfig: {
    width: 750,
    height: 1000,
    backgroundColor: '#fff',
    debug: false,
    blocks: [
      {
        x: 0,
        y: 10,
        width: 750, // 如果内部有文字，由文字宽度和内边距决定
        height: 120,
        paddingLeft: 0,
        paddingRight: 0,
        borderWidth: 10,
        borderColor: 'red',
        backgroundColor: 'blue',
        borderRadius: 40,
        text: {
          text: [
            {
              text: '金额¥ 1.00',
              fontSize: 80,
              color: 'yellow',
              opacity: 1,
              marginLeft: 50,
              marginRight: 10,
            },
            {
              text: '金额¥ 1.00',
              fontSize: 20,
              color: 'yellow',
              opacity: 1,
              marginLeft: 10,
              textDecoration: 'line-through',
            },
          ],
          baseLine: 'middle',
        },
      }
    ],
    texts: [
      {
        x: 0,
        y: 180,
        text: [
          {
            text: '长标题长标题长标题长标题长标题长标题长标题长标题长标题',
            fontSize: 40,
            color: 'red',
            opacity: 1,
            marginLeft: 0,
            marginRight: 10,
            width: 200,
            lineHeight: 40,
            lineNum: 2,
          },
          {
            text: '原价¥ 1.00',
            fontSize: 40,
            color: 'blue',
            opacity: 1,
            marginLeft: 10,
            textDecoration: 'line-through',
          },
        ],
        baseLine: 'middle',
      },
      {
        x: 10,
        y: 330,
        text: '金额¥ 1.00',
        fontSize: 80,
        color: 'blue',
        opacity: 1,
        baseLine: 'middle',
        textDecoration: 'line-through',
      },
    ],
    images: [
      {
        width: 62,
        height: 62,
        x: 30,
        y: 30,
        borderRadius: 62,
        url: 'https://www.wondfun.com/wanba/img/gamepic/wanba_qrcode.jpg',
      }
    ],
    lines: [
      {
        startY: 800,
        startX: 10,
        endX: 300,
        endY: 800,
        width: 5,
        color: 'red',
      }
    ]

  }
}
const app = getApp();
var initChart, aid, teamid, names, values = null;

Page({

  data: {
    posterConfig: posterConfig.jdConfig,
    ec: {
      avg: null,
      rate:null,
      lazyLoad: true // 延迟加载
    },
  },
  onPosterSuccess(e) {
    const { detail } = e;
    wx.previewImage({
      current: detail,
      urls: [detail]
    })
  },
  onPosterFail(err) {
    console.error(err);
  },
  onCreatePoster() {
    this.setData({ posterConfig: posterConfig.demoConfig }, () => {
      Poster.create(true);    // 入参：true为抹掉重新生成
    });
  },

  onCreateOtherPoster() {
    this.setData({ posterConfig: posterConfig.jdConfig }, () => {
      Poster.create(true);    // 入参：true为抹掉重新生成 
    });
  },
  onLoad(options) {
    let ops = JSON.parse(options.ops)
    aid=ops.aid
    teamid=ops.teamid
    console.log(ops)

    this.echartsComponnet = this.selectComponent('#mychart');
    this.fetch()
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getMyRadarData',
      data: {
        teamid: teamid,
        aid: aid
      },
      method: 'POST',
      success(res) {
        console.log(res.data)
        let data = res.data

        if (data) {
          let l = data.l
          
          let values = l.map((item) => {
            return item.value
          })
          console.log(values)
          let max=Math.max.apply(null,values)
          let names = l.map((item) => {
            let obj = {
              'name': item.name + ' ' + item.value,
              'max': max
            }
            return obj
          })
          console.log(names)
          that.setData({
            avg: data.avg,
            rate:data.rate
          })
          that.init_echarts(names,values)
        }
      },
      fail(res) {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },

  //初始化图表
  init_echarts: function (names, values) {
    this.echartsComponnet.init((canvas, width, height) => {
      // 初始化图表
      const Chart = echarts.init(canvas, null, {
        width: width,
        height: height
      });
      Chart.setOption(this.getOption(names, values));
      // 注意这里一定要返回 chart 实例，否则会影响事件处理等
      return Chart;
    });
  },
  getOption: function (names, values) {

    var option = {
      backgroundColor: "#121a21",
      color: ["#00a0e9"],
      tooltip: {},
      xAxis: {
        show: false
      },
      yAxis: {
        show: false
      },
      radar: {
        name: {
          color: '#946b25',
          fontSize: 14
        },
        splitArea: {
          show: true,
          areaStyle: {
            color: ['#121a21']
          }
        },
        splitLine: {
          lineStyle: {
            color: '#1c2d3e'
          }
        },

        axisLine: {
          lineStyle: {
            color: "#1c2d3e"
          }
        },
        // shape: 'circle',
        indicator:names
      },
      series: [{
        name: '玩霸指数',
        type: 'radar',
        symbol: 'circle',
        symbolSize: 8,
        areaStyle: {
          color: "#00a0e9",
          opacity: 0.3
        },
        data: [{
          value: values,
          name: '得分'
        }]
      }]
    };
    return option;
  },


});