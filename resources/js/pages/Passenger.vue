<template>
    <div class="page">
        <el-table :data="passengers" height="100%" stripe border class="passenger-table" v-loading="isPassengersLoading">
            <el-table-column prop="id_this" label="序号" width="60"></el-table-column>
            <el-table-column prop="name" label="姓名" width="120"></el-table-column>
            <el-table-column prop="id_type_name" label="证件类型" width="180"></el-table-column>
            <el-table-column prop="id_num" label="证件号码" width="200"></el-table-column>
            <el-table-column prop="gender_name" label="性别" width="60"></el-table-column>
            <el-table-column prop="mobile" label="手机号码" width="140"></el-table-column>
            <el-table-column label="乘客类型" width="100">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.type_name[1]" size="small">{{scope.row.type_name[0]}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column fixed="right" width="160">
                <template slot="header">
                    <el-button type="success" size="mini" @click="addDialogVisible = true"><i class="fas fa-plus" style="margin-right: 3px;"></i>添加</el-button>
                </template>
                <template slot-scope="scope">
                    <el-button type="primary" size="mini" @click="handleEdit(scope.row.id)">编辑</el-button>
                    <el-popconfirm
                        confirmButtonText='删除'
                        confirmButtonType='danger'
                        cancelButtonText='取消'
                        icon="el-icon-info"
                        iconColor="red"
                        title="您确定删除此乘客信息吗？"
                        @onConfirm="deletePassenger(scope.row.id, scope.row.is_self)"
                        >
                        <el-button slot="reference" type="danger" size="mini" :disabled="Boolean(scope.row.is_self)" :loading="isDeleting[scope.row.id]">删除</el-button>
                    </el-popconfirm>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog title="添加乘车人" :visible.sync="addDialogVisible" width="400px" :close-on-click-modal="false" @close="closeAddDialog">
            <el-form :model="addForm" :rules="rules" ref="addForm" label-width="auto" class="add-edit-form">
                <el-form-item label="姓名" prop="name">
                    <el-input v-model="addForm.name" placeholder="姓名"></el-input>
                </el-form-item>
                <el-form-item label="证件类型" prop="id_type">
                    <el-select v-model="addForm.id_type" placeholder="证件类型">
                        <el-option label="中国居民身份证" value="china_id"></el-option>
                        <el-option label="港澳居民来往内地通行证" value="hkmo_pass"></el-option>
                        <el-option label="台湾居民来往大陆通行证" value="tw_pass"></el-option>
                        <el-option label="护照" value="passport"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="证件号码" prop="id_num" :error="id_error">
                    <el-input v-model="addForm.id_num" placeholder="证件号码"></el-input>
                </el-form-item>
                <el-form-item label="性别" prop="gender">
                    <el-radio-group v-model="addForm.gender">
                        <el-radio border label="male">男</el-radio>
                        <el-radio border label="female">女</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="addForm.mobile" placeholder="手机号码"></el-input>
                </el-form-item>
                <el-form-item label="乘客类型" prop="type">
                    <el-select v-model="addForm.type" placeholder="乘客类型">
                        <el-option label="成人" value="adult"></el-option>
                        <el-option label="儿童" value="child"></el-option>
                        <el-option label="学生" value="student"></el-option>
                        <el-option label="残疾军人、伤残人民警察" value="military"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="add-dialog-footer">
                <el-button @click="closeAddDialog" :disabled="isAddLoading">取消</el-button>
                <el-button type="primary" @click="addPassenger" :loading="isAddLoading">确定</el-button>
            </div>
        </el-dialog>
        <el-dialog title="编辑乘车人信息" :visible.sync="editDialogVisible" width="400px" :close-on-click-modal="false" @close="closeEditdDialog">
            <el-form :model="editForm" :rules="rules" ref="editForm" label-width="auto" class="add-edit-form">
                <el-form-item label="姓名" prop="name">
                    <el-input v-model="editForm.name" placeholder="姓名"></el-input>
                </el-form-item>
                <el-form-item label="证件类型" prop="id_type">
                    <el-select v-model="editForm.id_type" placeholder="证件类型">
                        <el-option label="中国居民身份证" value="china_id"></el-option>
                        <el-option label="港澳居民来往内地通行证" value="hkmo_pass"></el-option>
                        <el-option label="台湾居民来往大陆通行证" value="tw_pass"></el-option>
                        <el-option label="护照" value="passport"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="证件号码" prop="id_num" :error="id_error">
                    <el-input v-model="editForm.id_num" placeholder="证件号码"></el-input>
                </el-form-item>
                <el-form-item label="性别" prop="gender">
                    <el-radio-group v-model="editForm.gender">
                        <el-radio border label="male">男</el-radio>
                        <el-radio border label="female">女</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="editForm.mobile" placeholder="手机号码"></el-input>
                </el-form-item>
                <el-form-item label="乘客类型" prop="type">
                    <el-select v-model="editForm.type" placeholder="乘客类型">
                        <el-option label="成人" value="adult"></el-option>
                        <el-option label="儿童" value="child"></el-option>
                        <el-option label="学生" value="student"></el-option>
                        <el-option label="残疾军人、伤残人民警察" value="military"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="add-dialog-footer">
                <el-button @click="closeEditdDialog" :disabled="isEditLoading">取消</el-button>
                <el-button type="primary" @click="editPassenger" :loading="isEditLoading">确定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    name: 'Passenger',
    data() {
        return {
            passengers: [],
            isPassengersLoading: true,
            isDeleting: [],
            addDialogVisible: false,
            addForm: {
                name: '',
                id_type: '',
                id_num: '',
                gender: '',
                mobile: '',
                type: ''

            },
            isAddLoading: false,
            editDialogVisible: false,
            editForm: {
                id: '',
                name: '',
                id_type: '',
                id_num: '',
                gender: '',
                mobile: '',
                type: ''
            },
            isEditLoading: false,
            rules: {
                name: [
                    { required: true, message: '请输入姓名', trigger: 'blur' },
                ],
                id_type: [
                    { required: true, message: '请选择证件类型', trigger: 'blur' },
                ],
                id_num: [
                    { required: true, message: '请输入证件号码', trigger: 'blur' },
                ],
                gender: [
                    { required: true, message: '请选择性别', trigger: 'blur' },
                ],
                mobile: [
                    { required: true, message: '请输入手机号码', trigger: 'blur' },
                ],
                type: [
                    { required: true, message: '请选择乘客类型', trigger: 'blur' },
                ],
            },
            id_error: '',
        }
    },
    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$axios.get('/api/passengers')
            .then(function (response) {
                for (let index = 0; index < response.data.length; index++) {
                    response.data[index].id_this = index + 1
                    switch (response.data[index].id_type) {
                        case 'china_id':
                            response.data[index].id_type_name = '中国居民身份证'
                            break;
                        case 'hkmo_pass':
                            response.data[index].id_type_name = '港澳居民来往内地通行证'
                            break;
                        case 'tw_pass':
                            response.data[index].id_type_name = '台湾居民来往大陆通行证'
                            break;
                        case 'passport':
                            response.data[index].id_type_name = '护照'
                            break;
                    }
                    switch (response.data[index].gender) {
                        case 'male':
                            response.data[index].gender_name = '男'
                            break;
                        case 'female':
                            response.data[index].gender_name = '女'
                            break;
                    }
                    switch (response.data[index].type) {
                        case 'adult':
                            response.data[index].type_name = ['成人', 'success']
                            break;
                        case 'child':
                            response.data[index].type_name = ['儿童', 'warning']
                            break;
                        case 'student':
                            response.data[index].type_name = ['学生', 'primary']
                            break;
                        case 'military':
                            response.data[index].type_name = ['伤残军警', 'danger']
                            break;
                    }
                }
                vm.passengers = response.data
                vm.isPassengersLoading = false
            })
        })
    },
    created() {
        this.$emit('getTitle', '乘车人管理');
    },
    methods: {
        deletePassenger(id, isSelf) {
            if (isSelf) {
                this.$message.error("您不能删除自己！");
            } else {
                this.isDeleting[id] = true
                this.$axios.delete('/api/passengers/' + id)
                    .then(resp => {
                        let res = resp.data;
                        if (resp.status == 200) {
                            this.isFollowed = true;
                            this.$message.success("删除成功！");
                            this.refreshPassengerList()
                            this.isDeleting[id] = false
                        } else {
                            this.$message.error("删除失败！");
                        }
                    }).catch(err => {
                        this.$message.error("删除失败！");
                    });
            }
        },
        refreshPassengerList() {
            this.isPassengersLoading = true
            var _t = this
            _t.$axios.get('/api/passengers')
                .then(function (response) {
                    for (let index = 0; index < response.data.length; index++) {
                        response.data[index].id_this = index + 1
                        switch (response.data[index].id_type) {
                            case 'china_id':
                                response.data[index].id_type_name = '中国居民身份证'
                                break;
                            case 'hkmo_pass':
                                response.data[index].id_type_name = '港澳居民来往内地通行证'
                                break;
                            case 'tw_pass':
                                response.data[index].id_type_name = '台湾居民来往大陆通行证'
                                break;
                            case 'passport':
                                response.data[index].id_type_name = '护照'
                                break;
                        }
                        switch (response.data[index].gender) {
                            case 'male':
                                response.data[index].gender_name = '男'
                                break;
                            case 'female':
                                response.data[index].gender_name = '女'
                                break;
                        }
                        switch (response.data[index].type) {
                            case 'adult':
                                response.data[index].type_name = ['成人', 'success']
                                break;
                            case 'child':
                                response.data[index].type_name = ['儿童', 'warning']
                                break;
                            case 'student':
                                response.data[index].type_name = ['学生', 'primary']
                                break;
                            case 'military':
                                response.data[index].type_name = ['残军伤警', 'danger']
                                break;
                        }
                    }
                    _t.passengers = response.data
                    _t.isPassengersLoading = false
                })
        },
        closeAddDialog() {
            this.addDialogVisible = false
            this.$refs['addForm'].resetFields()
        },
        closeEditdDialog() {
            this.editDialogVisible = false
            this.$refs['editForm'].resetFields()
        },
        addPassenger() {
            this.id_error = ''
            this.$refs['addForm'].validate((err) => {
                if (err) {
                    this.isAddLoading = true
                    this.$axios.post('/api/passengers', {
                        name: this.addForm.name,
                        id_type: this.addForm.id_type,
                        id_num: this.addForm.id_num,
                        gender: this.addForm.gender,
                        mobile: this.addForm.mobile,
                        type: this.addForm.type,
                    }).then((res) => {
                        const result = res.data
                        if (res.status == 201) {
                            this.isAddLoading = false
                            this.closeAddDialog()
                            this.refreshPassengerList()
                            this.$message({
                                showClose: true,
                                message: '添加成功',
                                type: 'success'
                            });
                        } else {
                            this.isAddLoading = false
                            this.$message({
                                showClose: true,
                                message: '添加失败，请稍后重试',
                                type: 'error'
                            });
                        }
                    }).catch((error) => {
                        this.$message({
                            showClose: true,
                            message: '添加失败：' + error.response.data.message,
                            type: 'error'
                        });
                        if (error.response.data.message.indexOf('身份证号码不合法') != -1) {
                            this.id_error = '身份证号码不合法'
                        }
                        this.isAddLoading = false
                    })
                }
            })
        },
        handleEdit(id) {
            this.editForm.id = id
            for (let index = 0; index < this.passengers.length; index++) {
                if (id == this.passengers[index].id) {
                    this.editForm.name = this.passengers[index].name
                    this.editForm.id_type = this.passengers[index].id_type
                    this.editForm.id_num = this.passengers[index].id_num
                    this.editForm.gender = this.passengers[index].gender
                    this.editForm.mobile = this.passengers[index].mobile
                    this.editForm.type = this.passengers[index].type
                    break
                }
            }
            this.editDialogVisible = true
        },
        editPassenger() {
            this.id_error = ''
            this.$refs['editForm'].validate((err) => {
                if (err) {
                    this.isEditLoading = true
                    this.$axios.patch('/api/passengers/' + this.editForm.id, {
                        name: this.editForm.name,
                        id_type: this.editForm.id_type,
                        id_num: this.editForm.id_num,
                        gender: this.editForm.gender,
                        mobile: this.editForm.mobile,
                        type: this.editForm.type,
                    }).then((res) => {
                        const result = res.data
                        if (res.status == 200) {
                            this.isEditLoading = false
                            this.closeEditdDialog()
                            this.refreshPassengerList()
                            this.$message({
                                showClose: true,
                                message: '编辑成功',
                                type: 'success'
                            });
                        } else {
                            this.isEditLoading = false
                            this.$message({
                                showClose: true,
                                message: '编辑失败，请稍后重试',
                                type: 'error'
                            });
                        }
                    }).catch((error) => {
                        this.$message({
                            showClose: true,
                            message: '编辑失败：' + error.response.data.message,
                            type: 'error'
                        });
                        if (error.response.data.message.indexOf('身份证号码不合法') != -1) {
                            this.id_error = '身份证号码不合法'
                        }
                        this.isEditLoading = false
                    })
                }
            })
        }
    },
}
</script>

<style lang="less" scoped>
.page {
    height: 100%;
    h1 {
        font-size: 20px;
        margin: 0 0 20px 0;
    }
    .passenger-table {
        width: 100%;
    }
}
.add-edit-form {
    .el-select {
        width: 100%;
    }
}
</style>
<style lang="less">
.body-card {
    .el-card__body {
        height: calc(100% - 101px);
    }
}
.passenger-table {
    th, td {
        text-align: center;
    }
}
</style>