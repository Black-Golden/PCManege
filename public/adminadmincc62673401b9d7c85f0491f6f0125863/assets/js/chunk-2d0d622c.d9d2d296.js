(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0d622c"],{"70eb":function(e,t,o){"use strict";o.r(t);var s=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"ele-body"},[o("el-card",{attrs:{shadow:"never"}},[o("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",{attrs:{label:"角色名称:"}},[o("el-input",{attrs:{placeholder:"请输入角色名称",clearable:""},model:{value:e.table.where.name,callback:function(t){e.$set(e.table.where,"name",t)},expression:"table.where.name"}})],1)],1),o("el-col",{attrs:{md:9,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),e.permission.includes("sys:role:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加")]):e._e(),e.permission.includes("sys:role:dall")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete"},on:{click:function(t){return e.remove()}}},[e._v("批量删除")]):e._e()],1)])],1)],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 260px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var s=t.index;return[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),o("el-table-column",{attrs:{type:"index",index:s,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),o("el-table-column",{attrs:{prop:"name",label:"角色名称",sortable:"custom","show-overflow-tooltip":"","min-width":"120",align:"center"}}),o("el-table-column",{attrs:{prop:"code",label:"角色编码","show-overflow-tooltip":"","min-width":"100",align:"center"}}),o("el-table-column",{attrs:{prop:"note",label:"备注","show-overflow-tooltip":"","min-width":"150",align:"center"}}),o("el-table-column",{attrs:{prop:"create_time",label:"创建时间","show-overflow-tooltip":"","min-width":"110",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.create_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"操作",width:"230px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var s=t.row;return[e.permission.includes("sys:role:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(s)}}},[e._v("修改")]):e._e(),e.permission.includes("sys:role:permission")?o("el-link",{attrs:{icon:"el-icon-finished",type:"primary",underline:!1},on:{click:function(t){return e.auth(s)}}},[e._v("分配权限")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此角色吗？"},on:{confirm:function(t){return e.remove(s)}}},[e.permission.includes("sys:role:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),o("el-dialog",{attrs:{title:e.editForm.id?"修改角色":"添加角色",visible:e.showEdit,width:"400px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"82px"}},[o("el-form-item",{attrs:{label:"角色名称:",prop:"name"}},[o("el-input",{attrs:{placeholder:"请输入角色名称",clearable:""},model:{value:e.editForm.name,callback:function(t){e.$set(e.editForm,"name",t)},expression:"editForm.name"}})],1),o("el-form-item",{attrs:{label:"角色标识:",prop:"code"}},[o("el-input",{attrs:{placeholder:"请输入角色标识",clearable:""},model:{value:e.editForm.code,callback:function(t){e.$set(e.editForm,"code",t)},expression:"editForm.code"}})],1),o("el-form-item",{attrs:{label:"备注:"}},[o("el-input",{attrs:{placeholder:"请输入备注",rows:4,type:"textarea"},model:{value:e.editForm.note,callback:function(t){e.$set(e.editForm,"note",t)},expression:"editForm.note"}})],1)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1),o("el-dialog",{attrs:{title:"分配权限",visible:e.showAuth,width:"400px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showAuth=t},closed:function(t){e.editForm={}}}},[o("el-scrollbar",{staticStyle:{height:"50vh"},attrs:{wrapStyle:"overflow-x: hidden;"}},[o("el-tree",{ref:"authTree",attrs:{data:e.authData,props:{label:"title"},"node-key":"id","default-expand-all":!0,"default-checked-keys":e.authChecked,"show-checkbox":""}})],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showAuth=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.saveAuth}},[e._v("保存")])],1)],1)],1)},a=[],r=(o("99af"),o("d81d"),o("5530")),l=o("2f62"),i={name:"SysRole",data:function(){return{table:{url:"/role/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{name:[{required:!0,message:"请输入角色名称",trigger:"blur"}],code:[{required:!0,message:"请输入角色编码",trigger:"blur"}]},showAuth:!1,authData:[]}},computed:Object(r["a"])(Object(r["a"])({},Object(l["b"])(["permission"])),{},{authChecked:function(){var e=[];return this.$util.eachTreeData(this.authData,(function(t){!t.checked||t.children&&t.children.length||e.push(t.id)})),e}}),methods:{edit:function(e){this.editForm=Object.assign({},e),this.showEdit=!0},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.$http.post("/role/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(e){var o=this.$loading({lock:!0});this.$http.post("/role/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var s=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的角色吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/role/delete",{id:s}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}},auth:function(e){var t=this;this.editForm=Object.assign({},e),this.authData=[];var o=this.$loading({background:"transparent"});this.$http.get("/role/getPermissionList?role_id="+e.id).then((function(e){o.close(),t.showAuth=!0,0===e.data.code?t.authData=t.$util.toTreeData(e.data.data,"id","pid"):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))},saveAuth:function(){var e=this,t=this.$loading({lock:!0}),o=this.$refs.authTree.getCheckedKeys().concat(this.$refs.authTree.getHalfCheckedKeys());this.$http.post("/role/savePermission?role_id="+this.editForm.id,o).then((function(o){t.close(),0===o.data.code?(e.showAuth=!1,e.$message({type:"success",message:o.data.msg})):e.$message.error(o.data.msg)})).catch((function(o){t.close(),e.$message.error(o.message)}))}}},n=i,c=o("2877"),d=Object(c["a"])(n,s,a,!1,null,"368d558d",null);t["default"]=d.exports}}]);