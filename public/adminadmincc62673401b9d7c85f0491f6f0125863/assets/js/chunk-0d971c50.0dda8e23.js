(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0d971c50"],{"6b95":function(e,t,l){"use strict";l.r(t);var o=function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",{staticClass:"ele-body"},[l("el-card",{attrs:{shadow:"never"}},[l("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[l("el-row",{attrs:{gutter:15}},[l("el-col",{attrs:{md:6,sm:12}},[l("el-form-item",{attrs:{label:"用户名:"}},[l("el-input",{attrs:{placeholder:"请输入用户名",clearable:""},model:{value:e.table.where.user_name,callback:function(t){e.$set(e.table.where,"user_name",t)},expression:"table.where.user_name"}})],1)],1),l("el-col",{attrs:{md:6,sm:12}},[l("el-form-item",{attrs:{label:"所属平台:"}},[l("el-select",{staticClass:"ele-fluid",attrs:{placeholder:"请选择所属平台",clearable:""},model:{value:e.table.where.platform_id,callback:function(t){e.$set(e.table.where,"platform_id",t)},expression:"table.where.platform_id"}},[l("el-option",{attrs:{label:"欧易",value:"2"}}),l("el-option",{attrs:{label:"币安",value:"3"}})],1)],1)],1),l("el-col",{attrs:{md:6,sm:12}},[l("div",{staticClass:"ele-form-actions"},[l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),l("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),l("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:quantsetupdown:dall")?l("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),l("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.index;return[l("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),l("el-table-column",{attrs:{type:"index",index:o,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),l("el-table-column",{attrs:{prop:"user_name",label:"用户",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"symbol",label:"代币类型",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"rounds",label:"补仓次数",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"down_stop_per",label:"补仓百分比",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"exp",label:"倍数",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"platform_id",label:"所属平台",sortable:"custom",resizable:!1,"min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[2==o.platform_id?l("span",{staticStyle:{color:"green"}},[e._v("欧易")]):e._e(),3==o.platform_id?l("span",{staticStyle:{color:"blue"}},[e._v("币安")]):e._e()]}}],null,!0)}),l("el-table-column",{attrs:{prop:"platform_type",label:"类别",sortable:"custom",resizable:!1,"min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[1==o.platform_type?l("span",{staticStyle:{color:"green"}},[e._v("现货")]):e._e(),2==o.platform_type?l("span",{staticStyle:{color:"blue"}},[e._v("合约")]):e._e()]}}],null,!0)}),l("el-table-column",{attrs:{prop:"setup_id",label:"关联setup_id",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"create_time",label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{prop:"update_time",label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),l("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[l("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此补仓设置吗？"},on:{confirm:function(t){return e.remove(o)}}},[e.permission.includes("sys:quantsetupdown:delete")?l("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),l("el-dialog",{attrs:{title:(e.editForm.id,"修改补仓设置"),visible:e.showEdit,width:"450px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[l("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"100px"}},[l("el-form-item",{attrs:{label:"用户：",prop:"user_id"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入用户"},model:{value:e.editForm.user_id,callback:function(t){e.$set(e.editForm,"user_id",t)},expression:"editForm.user_id"}})],1),l("el-form-item",{attrs:{label:"代币类型：",prop:"symbol"}},[l("el-input",{attrs:{placeholder:"请输入代币类型",clearable:""},model:{value:e.editForm.symbol,callback:function(t){e.$set(e.editForm,"symbol",t)},expression:"editForm.symbol"}})],1),l("el-form-item",{attrs:{label:"补仓次数：",prop:"rounds"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入补仓次数"},model:{value:e.editForm.rounds,callback:function(t){e.$set(e.editForm,"rounds",t)},expression:"editForm.rounds"}})],1),l("el-form-item",{attrs:{label:"补仓百分比：",prop:"down_stop_per"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入补仓百分比"},model:{value:e.editForm.down_stop_per,callback:function(t){e.$set(e.editForm,"down_stop_per",t)},expression:"editForm.down_stop_per"}})],1),l("el-form-item",{attrs:{label:"倍数：",prop:"exp"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入倍数"},model:{value:e.editForm.exp,callback:function(t){e.$set(e.editForm,"exp",t)},expression:"editForm.exp"}})],1),l("el-form-item",{attrs:{label:"所属平台：",prop:"platform_id"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入所属平台"},model:{value:e.editForm.platform_id,callback:function(t){e.$set(e.editForm,"platform_id",t)},expression:"editForm.platform_id"}})],1),l("el-form-item",{attrs:{label:"1是现货2是合约：",prop:"platform_type"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入1是现货2是合约"},model:{value:e.editForm.platform_type,callback:function(t){e.$set(e.editForm,"platform_type",t)},expression:"editForm.platform_type"}})],1),l("el-form-item",{attrs:{label:"关联setup_id：",prop:"setup_id"}},[l("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入关联setup_id"},model:{value:e.editForm.setup_id,callback:function(t){e.$set(e.editForm,"setup_id",t)},expression:"editForm.setup_id"}})],1)],1),l("div",{attrs:{slot:"footer"},slot:"footer"},[l("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),l("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},r=[],s=(l("d81d"),l("5530")),a=l("2f62"),i={name:"SysQuantSetupDown",data:function(){return{table:{url:"/quantsetupdown/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{user_id:[{required:!0,message:"请输入用户",trigger:"blur"}],symbol:[{required:!0,message:"请输入代币类型",trigger:"blur"}],rounds:[{required:!0,message:"请输入补仓次数",trigger:"blur"}],down_stop_per:[{required:!0,message:"请输入补仓百分比",trigger:"blur"}],exp:[{required:!0,message:"请输入倍数",trigger:"blur"}],platform_id:[{required:!0,message:"请输入所属平台",trigger:"blur"}],platform_type:[{required:!0,message:"请输入1是现货2是合约",trigger:"blur"}],setup_id:[{required:!0,message:"请输入关联setup_id",trigger:"blur"}]}}},computed:Object(s["a"])({},Object(a["b"])(["permission"])),mounted:function(){},methods:{edit:function(e){var t=this;this.$http.get("/quantsetupdown/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var l=e.$loading({lock:!0});e.$http.post("/quantsetupdown/edit",e.editForm).then((function(t){l.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){l.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var l=this.$loading({lock:!0});this.$http.post("/quantsetupdown/delete",{id:e.id}).then((function(e){l.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){l.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var o=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的补仓设置吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/quantsetupdown/delete",{id:o}).then((function(l){e.close(),0===l.data.code?(t.$message({type:"success",message:l.data.msg}),t.$refs.table.reload()):t.$message.error(l.data.msg)})).catch((function(l){e.close(),t.$message.error(l.message)}))})).catch((function(){return 0}))}}}},n=i,c=(l("9635"),l("2877")),d=Object(c["a"])(n,o,r,!1,null,"36ae4836",null);t["default"]=d.exports},9635:function(e,t,l){"use strict";l("e300")},e300:function(e,t,l){}}]);