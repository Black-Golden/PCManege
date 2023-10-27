<template>
  <div class="ele-body">
    <!-- 顶部卡片 -->
    <el-card shadow="never" body-style="padding: 20px;">

    </el-card>
    <el-row :gutter="15">

    欢迎光临
    </el-row>
  </div>
</template>

<script>
import ElTableDraggable from '@/components/Sortable/ElTableDraggable'

export default {
  name: "Workplace",
  components: {ElTableDraggable},
  data() {
    return {
      activities: [
        {title: 'SunSmile 解决了bug 登录提示操作失败', timestamp: '20:30', primary: false},
        {title: 'Jasmine 解决了bug 按钮颜色与设计不符', timestamp: '19:30', primary: false},
        {title: '项目经理 指派了任务 解决项目一的bug', timestamp: '18:30', primary: true},
        {title: '项目经理 指派了任务 解决项目二的bug', timestamp: '17:30', primary: true},
        {title: '项目经理 指派了任务 解决项目三的bug', timestamp: '16:30', primary: true},
        {title: '项目经理 指派了任务 解决项目四的bug', timestamp: '15:30', primary: false},
        {title: '项目经理 指派了任务 解决项目五的bug', timestamp: '14:30', primary: false},
        {title: '项目经理 指派了任务 解决项目六的bug', timestamp: '12:30', primary: false},
        {title: '项目经理 指派了任务 解决项目七的bug', timestamp: '11:30', primary: true},
        {title: '项目经理 指派了任务 解决项目八的bug', timestamp: '10:30', primary: false},
        {title: '项目经理 指派了任务 解决项目九的bug', timestamp: '09:30', primary: false},
        {title: '项目经理 指派了任务 解决项目十的bug', timestamp: '08:30', primary: false}
      ],
      taskList: [
        {id: 1, priority: 1, taskName: '解决项目一的bug', state: 0},
        {id: 2, priority: 2, taskName: '解决项目二的bug', state: 0},
        {id: 3, priority: 2, taskName: '解决项目三的bug', state: 1},
        {id: 4, priority: 3, taskName: '解决项目四的bug', state: 1},
        {id: 5, priority: 3, taskName: '解决项目五的bug', state: 2},
        {id: 6, priority: 3, taskName: '解决项目六的bug', state: 2}
      ],
      projectList: [
        {projectName: '人工智能项目立项申请', state: 0, startDate: '2021-03-15', endDate: '2021-04-15', progress: 30},
        {projectName: 'AI智能芯片设计展', state: 0, startDate: '2021-03-01', endDate: '2021-03-05', progress: 10},
        {projectName: '智慧医疗推进会', state: 1, startDate: '2021-02-15', endDate: '2021-02-20', progress: 60},
        {projectName: '大数据智能分析平台建设', state: 1, startDate: '2021-02-10', endDate: '2021-02-22', progress: 0},
        {projectName: '云计算大数据分析', state: 2, startDate: '2021-02-05', endDate: '2021-02-25', progress: 100},
        {projectName: 'SpaceX火箭发射计划', state: 3, startDate: '2021-01-20', endDate: '2021-01-25', progress: 100},
        {projectName: '新能源汽车设计大赛', state: 3, startDate: '2021-01-01', endDate: '2021-01-08', progress: 100}
      ],
      userList: [
        {
          name: '詹姆斯·高斯林', desc: 'UI设计师、交互专家', state: 0,
          avatar: require('@/assets/logo.png')
        },
        {
          name: '詹姆斯·高斯林', desc: '前端工程师', state: 0,
          avatar: require('@/assets/logo.png')
        },
        {
          name: '詹姆斯·高斯林', desc: '前端工程师', state: 0,
          avatar: require('@/assets/logo.png')
        },
        {
          name: '詹姆斯·高斯林', desc: '产品经理、项目经理', state: 1,
          avatar: require('@/assets/logo.png')
        },
        {
          name: '詹姆斯·高斯林', desc: '组长、后端工程师', state: 1,
          avatar: require('@/assets/logo.png')
        }
      ]
    }
  },
  mounted() {

  }
}
</script>

<style scoped>
.ele-body {
  padding-bottom: 0;
}

.el-card {
  margin-bottom: 15px;
}

/** 顶部统计 */
.workplace-count-group {
  white-space: nowrap;
}

.workplace-count-item {
  padding: 0 5px 0 25px;
  box-sizing: border-box;
  display: inline-block;
  text-align: right;
}

.workplace-count-name {
  padding-left: 5px;
}

.workplace-count-num {
  font-size: 23px;
  margin-top: 6px;
}

@media screen and (max-width: 992px) {
  .workplace-count-item {
    padding: 0 5px 0 10px;
  }
}

@media screen and (max-width: 768px) {
  .workplace-user-card, .workplace-count-group {
    display: block;
  }

  .workplace-count-group {
    margin-top: 15px;
    text-align: right;
  }
}

/** 快捷方式 */
.app-link-block {
  cursor: pointer;
  text-align: center;
  padding: 15px 0;
}

.app-link-block .app-link-icon {
  color: #69c0ff;
  font-size: 30px;
  margin-top: 5px;
}

.app-link-block .app-link-title {
  margin-top: 8px;
}

/** 最新动态时间轴 */
.ele-timeline-act {
  padding-left: 50px;
}

.ele-timeline-act >>> .el-timeline-item__timestamp {
  margin: 0;
  position: absolute;
  top: 3px;
  left: -45px;
}

.ele-timeline-act >>> .el-timeline-item {
  padding-bottom: 19px;
}

.ele-timeline-act >>> .el-timeline-item:last-child {
  padding-bottom: 0;
}

/** 表格 */
.workplace-table-card >>> .el-table tbody > .el-table__row:last-child td {
  border-bottom: none;
}

.workplace-table-card >>> .el-table:before {
  display: none;
}

.sort-handle {
  cursor: move;
  font-size: 18px;
  vertical-align: middle;
}

.ele-body >>> .el-table__row.sortable-chosen {
  background-color: hsla(0, 0%, 60%, .1);
}

.ele-body >>> .el-table__row.sortable-chosen td {
  background-color: transparent;
}

/** 本月目标 */
.workplace-goal-group {
  text-align: center;
  position: relative;
  padding: 48px 0;
}

.workplace-goal-group .workplace-goal-content {
  position: absolute;
  top: 90px;
  left: 0;
  width: 100%;
}

.workplace-goal-group .workplace-goal-num {
  font-size: 40px;
  margin-top: 15px;
}

/** 小组成员 */
.user-list-item {
  padding: 13px 18px;
}

.user-list-item + .user-list-item {
  border-top: 1px solid hsla(0, 0%, 60%, .15);
}

.user-list-item .ele-cell-desc {
  margin-top: 5px;
}
</style>
