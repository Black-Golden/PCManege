<template>
	<div class="ele-body">
		<el-card shadow="never">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="6" :sm="12">
						<el-form-item label="发起人:">
							<el-input v-model="table.where.user_id_from_name" placeholder="请输入发起人" clearable />
						</el-form-item> </el-col><el-col :md="6" :sm="12">
						<el-form-item label="接受人:">
							<el-input v-model="table.where.user_id_to_name" placeholder="请输入接受人" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="9" :sm="19">
						<div class="block">
							<!-- <span class="demonstration">搜索时间</span> -->
							<el-form-item label="搜索时间">
								<el-date-picker @change="rq_change" value-format="yyyy-MM-dd HH:mm"
									style="margin-left:10px" v-model="create_time" type="datetimerange"
									start-placeholder="开始日期" end-placeholder="结束日期">
								</el-date-picker></el-form-item>
						</div>
					</el-col>
				</el-row>
				<el-col :md="6" :sm="12">
					<div class="ele-form-actions">
						<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
							class="ele-btn-icon">查询
						</el-button>
						<el-button type="success" @click="handleDownload()" icon="el-icon-download"
							class="ele-btn-icon">导出
						</el-button>
						<el-button @click=";(table.where = {}) && $refs.table.reload()">重置</el-button>
					</div>
				</el-col>
			</el-form>
			<!-- 操作按钮 -->
			<!-- <div class="ele-table-tool ele-table-tool-default">
               <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantmove:add')">添加
               </el-button>
               <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantmove:dall')">批量删除
               </el-button>
      </div> -->
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{ index }">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="user_from" label="发起人" sortable="custom" show-overflow-tooltip
						min-width="120">
						<template slot-scope="{ row }">
							<div :style="{ color: 'green' }">{{ row.user_from }}</div>
						</template>
					</el-table-column>

					<el-table-column prop="user_to" label="接受人" sortable="custom" show-overflow-tooltip
						min-width="120"><template slot-scope="{ row }">
							<div :style="{ color: '#0467aa' }">{{ row.user_to }}</div>
						</template>
					</el-table-column>

					<!-- <el-table-column
            prop="wallet"
            label="钱包类型"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          /> -->

					<el-table-column prop="amount" label="转入数量" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="fee" label="手续费" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="score_begin" label="转入前数量" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="score_end" label="转入后数量" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<!-- <el-table-column
            prop="oper"
            label="备注"
            sortable="custom"
            show-overflow-tooltip
            min-width="120"
          /> -->

					<el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip
						min-width="120" />
					<el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip
						min-width="120" />
					<!--          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">-->
					<!--            <template slot-scope="{row}">-->
					<!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantmove:edit')">修改</el-link>&ndash;&gt;-->
					<!--              <el-popconfirm title="确定要删除此用户转出吗？" @confirm="remove(row)" class="ele-action">-->
					<!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantmove:delete')">删除</el-link>-->
					<!--              </el-popconfirm>-->
					<!--            </template>-->
					<!--          </el-table-column>-->
				</template>
			</ele-data-table>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id ? '修改用户转出' : '修改用户转出'" :visible.sync="showEdit" width="450px"
			@closed="editForm = {}" :destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
				<el-form-item label="发起人：" prop="user_id_from">
					<el-input-number v-model="editForm.user_id_from" controls-position="right" :min="0"
						placeholder="请输入发起人" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="接受人：" prop="user_id_to">
					<el-input-number v-model="editForm.user_id_to" controls-position="right" :min="0"
						placeholder="请输入接受人" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="钱包类型：" prop="wallet">
					<el-input v-model="editForm.wallet" placeholder="请输入钱包类型" clearable />
				</el-form-item>

				<el-form-item label="转入数量：" prop="num">
					<el-input v-model="editForm.num" placeholder="请输入转入数量" clearable />
				</el-form-item>

				<el-form-item label="手续费：" prop="tax">
					<el-input-number v-model="editForm.tax" controls-position="right" :min="0" placeholder="请输入手续费"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="转入前数量：" prop="score_begin">
					<el-input-number v-model="editForm.score_begin" controls-position="right" :min="0"
						placeholder="请输入转入前数量" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="转入后数量：" prop="score_end">
					<el-input-number v-model="editForm.score_end" controls-position="right" :min="0"
						placeholder="请输入转入后数量" class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="备注：" prop="oper">
					<el-input v-model="editForm.oper" placeholder="请输入备注" clearable />
				</el-form-item>
			</el-form>
			<div slot="footer">
				<el-button @click="showEdit = false">取消</el-button>
				<el-button type="primary" @click="save">保存</el-button>
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from 'vuex'
	export default {
		name: 'SysQuantMove',
		data() {
			return {
				export_list:[],
				count:"",
				create_time: '',
				table: {
					url: '/quantmove/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: {
					// 表单验证规则

					user_id_from: [{
						required: true,
						message: '请输入发起人',
						trigger: 'blur'
					}, ],

					user_id_to: [{
						required: true,
						message: '请输入接受人',
						trigger: 'blur'
					}, ],

					wallet: [{
						required: true,
						message: '请输入钱包类型',
						trigger: 'blur'
					}, ],

					num: [{
						required: true,
						message: '请输入转入数量',
						trigger: 'blur'
					}],

					tax: [{
						required: true,
						message: '请输入手续费',
						trigger: 'blur'
					}],

					score_begin: [{
						required: true,
						message: '请输入转入前数量',
						trigger: 'blur'
					}, ],

					score_end: [{
						required: true,
						message: '请输入转入后数量',
						trigger: 'blur'
					}, ],

					oper: [{
						required: true,
						message: '请输入备注',
						trigger: 'blur'
					}],
				},
			}
		},
		computed: {
			...mapGetters(['permission']),
		},
		mounted() {
			this.account_details()
		},
		methods: {
			rq_change(e) {
				this.table.where.start_time = e[0]
				this.table.where.end_time = e[1]
			},
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http
					.get('/quantmove/info?id=' + row.id)
					.then((res) => {
						if (res.data.code === 0) {
							this.editForm = res.data.data
							this.showEdit = true
						} else {
							this.$message.error(res.data.msg)
						}
					})
					.catch((e) => {
						this.$message.error(e.message)
					})
			},
			/**
			 * 保存编辑
			 */
			save() {
				this.$refs['editForm'].validate((valid) => {
					if (valid) {
						const loading = this.$loading({
							lock: true
						})
						this.$http
							.post('/quantmove/edit', this.editForm)
							.then((res) => {
								loading.close()
								if (res.data.code === 0) {
									this.showEdit = false
									this.$message({
										type: 'success',
										message: res.data.msg
									})
									this.$refs.table.reload()
								} else {
									this.$message.error(res.data.msg)
								}
							})
							.catch((e) => {
								loading.close()
								this.$message.error(e.message)
							})
					} else {
						return false
					}
				})
			},
			/**
			 * 刪除(批量刪除)
			 */
			remove(row) {
				console.log(row)
				if (!row) {
					// 批量删除
					if (this.choose.length === 0)
						return this.$message.error('请至少选择一条数据')
					let ids = this.choose.map((d) => d.id)
					this.$confirm('确定要删除选中的用户转出吗?', '提示', {
							type: 'warning',
						})
						.then(() => {
							const loading = this.$loading({
								lock: true
							})
							this.$http
								.post('/quantmove/delete', {
									id: ids
								})
								.then((res) => {
									loading.close()
									if (res.data.code === 0) {
										this.$message({
											type: 'success',
											message: res.data.msg
										})
										this.$refs.table.reload()
									} else {
										this.$message.error(res.data.msg)
									}
								})
								.catch((e) => {
									loading.close()
									this.$message.error(e.message)
								})
						})
						.catch(() => 0)
				} else {
					// 单个删除
					const loading = this.$loading({
						lock: true
					})
					this.$http
						.post('/quantmove/delete', {
							id: row.id
						})
						.then((res) => {
							loading.close()

							if (res.data.code === 0) {
								this.$message({
									type: 'success',
									message: res.data.msg
								})
								this.$refs.table.reload()
							} else {
								this.$message.error(res.data.msg)
							}
						})
						.catch((e) => {
							loading.close()
							this.$message.error(e.message)
						})
				}
			},
			account_details() {
				this.$http.get('/quantmove/index').then(res => {
					if (res.data.code === 0) {
						this.count = res.data.count
						console.log(this.count)
					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});

			},
			handleDownload(index) {
				this.table.where.limit = this.count
				this.table.where.page = 1
				this.$http.post('/quantmove/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						// for (var i = 0; i < this.export_list.length; i++) {
						// 	this.export_list[i].create_time = getLocalTime(this.export_list[i]
						// 		.create_time);
						// 	this.export_list[i].update_time = getLocalTime(this.export_list[i]
						// 		.update_time);
						// }
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'发起人',
								'接受人',
								'转入数量',
								'手续费',
								'转入前数量',
								'转入后数量',
								'创建时间',
								'更新时间',

							]
							const filterVal = [
								'user_from',
								'user_to',
								'amount',
								'fee',
								'score_begin',
								'score_end',
								'create_time',
								'update_time',

							]
							const list = this.export_list
							const filename = '转账记录'
							const data = this.formatJson(filterVal, list)
							excel.export_json_to_excel({
								header,
								data,
								filename
							})
							this.downloadLoading = false
							this.downloadLoading1 = false
						})

					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});
			},
			formatJson(filterVal, jsonData) {
				return jsonData.map(v => filterVal.map(j => {
					if (j === 'timestamp') {
						return parseTime(v[j])
					} else {
						return v[j]
					}
				}))
			},
		},
	}

	function getLocalTime(nS) {
		return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/, ' ');
	}
</script>

<style scoped>
	.ele-block>>>.el-upload,
	.ele-block>>>.el-upload-dragger {
		width: 100%;
	}
</style>