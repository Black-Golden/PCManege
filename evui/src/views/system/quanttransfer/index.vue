<template>
	<div class="ele-body">
		<el-card shadow="never" style="position: relative;">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="5" :sm="12">
						<el-form-item label="用户名:">
							<el-input v-model="table.where.user_name" placeholder="请输入用户名" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="12" :sm="19">
						<div class="block">
							<!-- <span class="demonstration">搜索时间</span> -->
							<el-form-item label="搜索时间">
								<el-date-picker @change="rq_change" value-format="yyyy-MM-dd HH:mm"
									style="margin-left:10px" v-model="create_time" type="datetimerange"
									start-placeholder="开始日期" end-placeholder="结束日期">
								</el-date-picker></el-form-item>
						</div>
					</el-col>
					<el-col :md="10" :sm="12">
						<div class="ele-form-actions">
							<el-button type="success" @click="handleDownload()" icon="el-icon-download"
								class="ele-btn-icon">导出
							</el-button>
							<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
								class="ele-btn-icon">查询
							</el-button>
							<el-button @click=";(table.where = {}) && $refs.table.reload()">重置</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<div class="ele-table-tool ele-table-tool-default">
				<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quanttransfer:add')">添加-->
				<!--        </el-button>-->
				<!--        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quanttransfer:dall')">批量删除-->
				<!--        </el-button>-->
			</div>
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{ index }">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="user_name" label="充值用户" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="transactionHash" label="交易哈希" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="from" label="来源地址" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="to" label="充值地址" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="value" label="金额" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="create_time" label="创建时间" sortable="custom" show-overflow-tooltip
						min-width="120" />
					<!-- <el-table-column prop="update_time" label="更新时间" sortable="custom" show-overflow-tooltip min-width="120"/> -->
					<!--          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">-->
					<!--            <template slot-scope="{row}">-->
					<!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quanttransfer:edit')">修改</el-link>&ndash;&gt;-->
					<!--              <el-popconfirm title="确定要删除此充值吗？" @confirm="remove(row)" class="ele-action">-->
					<!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quanttransfer:delete')">删除</el-link>-->
					<!--              </el-popconfirm>-->
					<!--            </template>-->
					<!--          </el-table-column>-->
				</template>
			</ele-data-table>
			<div class="collect"><span>汇总</span> {{ sum }}</div>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id ? '修改充值' : '修改充值'" :visible.sync="showEdit" width="450px" @closed="editForm = {}"
			:destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
				<el-form-item label="充值用户：" prop="user_id">
					<el-input-number v-model="editForm.user_id" controls-position="right" :min="0" placeholder="请输入充值用户"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="交易哈希：" prop="transactionHash">
					<el-input v-model="editForm.transactionHash" placeholder="请输入交易哈希" clearable />
				</el-form-item>

				<el-form-item label="来源地址：" prop="from">
					<el-input v-model="editForm.from" placeholder="请输入来源地址" clearable />
				</el-form-item>

				<el-form-item label="充值地址：" prop="to">
					<el-input v-model="editForm.to" placeholder="请输入充值地址" clearable />
				</el-form-item>

				<el-form-item label="金额：" prop="value">
					<el-input-number v-model="editForm.value" controls-position="right" :min="0" placeholder="请输入金额"
						class="ele-fluid ele-text-left" />
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
		name: 'SysQuantTransfer',
		data() {
			return {
				export_list: [],
				count: "",
				create_time: '',
				sum: '',
				table: {
					url: '/quanttransfer/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: {
					// 表单验证规则

					user_id: [{
						required: true,
						message: '请输入充值用户',
						trigger: 'blur'
					}, ],

					transactionHash: [{
						required: true,
						message: '请输入交易哈希',
						trigger: 'blur'
					}, ],

					from: [{
						required: true,
						message: '请输入来源地址',
						trigger: 'blur'
					}],

					to: [{
						required: true,
						message: '请输入充值地址',
						trigger: 'blur'
					}],

					value: [{
						required: true,
						message: '请输入金额',
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
		created() {
			this.getSum()
		},
		methods: {
			rq_change(e) {
				this.table.where.start_time = e[0]
				this.table.where.end_time = e[1]
			},
			getSum() {
				this.$http.get('/quanttransfer/index').then((res) => {
					if (res.data.code === 0) {
						console.log(res.data)
						this.sum = res.data.sum
					}
				})
			},
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http
					.get('/quanttransfer/info?id=' + row.id)
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
							.post('/quanttransfer/edit', this.editForm)
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
					this.$confirm('确定要删除选中的充值吗?', '提示', {
							type: 'warning'
						})
						.then(() => {
							const loading = this.$loading({
								lock: true
							})
							this.$http
								.post('/quanttransfer/delete', {
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
						.post('/quanttransfer/delete', {
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
				this.$http.get('/quanttransfer/index').then(res => {
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
				this.$http.post('/quanttransfer/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						for (var i = 0; i < this.export_list.length; i++) {
							if (this.export_list[i].is_check == 1) {
								this.export_list[i].is_check = '已审核'
							} else if (this.export_list[i].is_check == 0) {
								this.export_list[i].is_check = '未审核'
							} else if (this.export_list[i].is_check == 2) {
								this.export_list[i].is_check = '已拒绝'
							}
							// this.export_list[i].is_check = getLocalTime(this.export_list[i]
							// 	.create_time);
							// this.export_list[i].update_time = getLocalTime(this.export_list[i]
							// 	.update_time);
						}
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'充值用户',
								'交易哈希',
								'来源地址',
								'充值地址',
								'金额',

								'创建时间',



							]
							const filterVal = [
								'user_name',
								'transactionHash',
								'from',
								'to',
								'value',
								'create_time',


							]
							const list = this.export_list
							const filename = '充值流水'
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

	.collect {
		position: absolute;
		left: 1%;
		bottom: 2%;
		/* bottom: 50%; */
		padding: 10px;
		/* background-color: pink; */
	}

	.collect span {
		font-weight: 600;
	}
</style>