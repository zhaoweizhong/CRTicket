<template>
    <div class="page">
        <el-table :data="trains.data" height="100%" stripe border class="train-table" v-loading="isTrainsLoading">
            <el-table-column label="车次" width="70">
                <template slot-scope="scope">
                    <span @click="openTimetableDialog(scope.row)">{{JSON.parse(scope.row.numbers)[0]}}</span>
                </template>
            </el-table-column>
            <el-table-column label="出发站" width="90">
                <template slot-scope="scope">
                    {{scope.row.stations[0].name}}
                </template>
            </el-table-column>
            <el-table-column label="到达站" width="90">
                <template slot-scope="scope">
                    {{scope.row.stations[scope.row.stations.length-1].name}}
                </template>
            </el-table-column>
            <el-table-column label="出发时间" width="80">
                <template slot-scope="scope">
                    {{scope.row.stations[0].pivot.depart_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.stations[0].pivot.depart_time.substr(0,2))[1])+scope.row.stations[0].pivot.depart_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}<br/>{{scope.row.stations[0].pivot.depart_time ? (getDays(scope.row.stations[0].pivot.depart_time)[0] > 0 ? '（+'+getDays(scope.row.stations[0].pivot.depart_time.substr(0,2))[0]+'天）':'') : ''}}
                </template>
            </el-table-column>
            <el-table-column label="到达时间" width="80">
                <template slot-scope="scope">
                    {{scope.row.stations[scope.row.stations.length-1].pivot.arrive_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.stations[scope.row.stations.length-1].pivot.arrive_time.substr(0,2))[1])+scope.row.stations[scope.row.stations.length-1].pivot.arrive_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}<br/>{{scope.row.stations[scope.row.stations.length-1].pivot.arrive_time ? (getDays(scope.row.stations[scope.row.stations.length-1].pivot.arrive_time.substr(0,2))[0] > 0 ? '（+'+getDays(scope.row.stations[scope.row.stations.length-1].pivot.arrive_time.substr(0,2))[0]+'天）':'') : ''}}
                </template>
            </el-table-column>
            <el-table-column label="历时" width="70">
                <template slot-scope="scope">
                    {{calcTimeDiff(scope.row)}}
                </template>
            </el-table-column>
            <el-table-column label="途径站数" width="80">
                <template slot-scope="scope">
                    {{scope.row.stations.length}}
                </template>
            </el-table-column>
            <el-table-column label="上次修改" width="160">
                <template slot-scope="scope">
                    {{$moment(scope.row.created_at).format('YYYY-MM-DD HH:mm:ss')}}
                </template>
            </el-table-column>
            <el-table-column label="状态" width="80">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.status == 1 ? 'success' : 'danger'" size="medium">{{scope.row.status == 1 ? '正常' : '停开'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column fixed="right" width="220">
                <template slot="header">
                    <el-button type="success" size="mini" @click="addDialogVisible = true"><i class="fas fa-plus" style="margin-right: 3px;"></i>添加</el-button>
                </template>
                <template slot-scope="scope">
                    <el-button size="mini" @click="openTimetableDialog(scope.row)">详情</el-button>
                    <el-button type="primary" size="mini" @click="editTrain(scope.row)">编辑</el-button>
                    <el-popconfirm
                        style="display: unset; margin-left: 10px;"
                        confirmButtonText='删除'
                        confirmButtonType='danger'
                        cancelButtonText='取消'
                        icon="el-icon-info"
                        iconColor="red"
                        title="您确定删除此次列车吗？"
                        @onConfirm="deleteTrain(scope.row)"
                        >
                        <el-button slot="reference" type="danger" size="mini">删除</el-button>
                    </el-popconfirm>
                </template>
            </el-table-column>
        </el-table>
        <div class="pagination">
            <el-pagination
            background
            layout="prev, pager, next"
            :current-page="paginate.meta.current_page"
            :total="paginate.meta.total"
            :page-size="15"
            @next-click="changePage"
            @prev-click="changePage"
            @current-change="changePage"
            ></el-pagination>
        </div>
        <el-dialog :title="timetable.train_num + '次 时刻表'" :visible.sync="timetableDialogVisible" width="500px" class="timetable-dialog">
            <el-table :data="timetable.stations" height="100%" stripe border class="timetable-table" header-cell-class-name="table-cell" cell-class-name="table-cell">
                <el-table-column label="站序" width="61">
                    <template slot-scope="scope">
                        {{scope.row.pivot.station_num}}
                    </template>
                </el-table-column>
                <el-table-column label="车站" width="101">
                    <template slot-scope="scope">
                        {{scope.row.name}}
                    </template>
                </el-table-column>
                <el-table-column label="到站时间" width="99">
                    <template slot-scope="scope">
                        {{scope.row.pivot.arrive_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.pivot.arrive_time.substr(0,2))[1])+scope.row.pivot.arrive_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}<br/>{{scope.row.pivot.arrive_time ? (getDays(scope.row.pivot.arrive_time.substr(0,2))[0] > 0 ? '（+'+getDays(scope.row.pivot.arrive_time.substr(0,2))[0]+'天）':'') : ''}}
                    </template>
                </el-table-column>
                <el-table-column label="出发时间" width="99">
                    <template slot-scope="scope">
                        {{scope.row.pivot.depart_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.pivot.depart_time.substr(0,2))[1])+scope.row.pivot.depart_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}<br/>{{scope.row.pivot.depart_time ? (getDays(scope.row.pivot.depart_time.substr(0,2))[0] > 0 ? '（+'+getDays(scope.row.pivot.depart_time.substr(0,2))[0]+'天）':'') : ''}}
                    </template>
                </el-table-column>
                <el-table-column label="停留时间" width="99">
                    <template slot-scope="scope">
                        {{(scope.row.pivot.arrive_time && scope.row.pivot.depart_time) ? calcTimeDiff2(scope.row.pivot.depart_time,scope.row.pivot.arrive_time) : '-'}}
                    </template>
                </el-table-column>
            </el-table>
            <div slot="footer" class="timetable-dialog-footer">
                <el-button @click="timetableDialogVisible = false">关闭</el-button>
            </div>
        </el-dialog>
        <el-dialog title="添加列车" :visible.sync="addDialogVisible" width="1000px" :close-on-click-modal="false" class="add-edit-dialog">
            <el-form :model="addForm" ref="addForm" label-width="auto" class="add-edit-form">
                <el-row :gutter="5">
                    <el-col :span="12" v-for="(station, index) in addForm.stations" :key="index">
                        <el-card shadow="never" class="stations-card">
                            <div class="title">
                                <span>第{{index + 1}}站</span>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <el-form-item label="车站">
                                        <el-select
                                            v-model="station.id"
                                            filterable
                                            remote
                                            size="small"
                                            placeholder="简拼/全拼/汉字"
                                            :remote-method="selectSearch"
                                            :loading="selectSearchLoading"
                                            class="input">
                                            <el-option
                                            v-for="item in selectSearchResult"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="车次">
                                        <el-input v-model="station.number" placeholder="车次" size="small" class="input"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="12">
                                    <el-form-item label="到时">
                                        <el-input-number placeholder="时" v-model="station.arriveTime[0]" controls-position="right" :min="0" :disabled="index == 0" size="small"></el-input-number>
                                        <el-input-number placeholder="分" v-model="station.arriveTime[1]" controls-position="right" :min="0" :disabled="index == 0" size="small"></el-input-number>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="发时">
                                        <el-input-number placeholder="时" v-model="station.departTime[0]" controls-position="right" :min="0" :disabled="index == addForm.stations.length - 1" size="small"></el-input-number>
                                        <el-input-number placeholder="分" v-model="station.departTime[1]" controls-position="right" :min="0" :disabled="index == addForm.stations.length - 1" size="small"></el-input-number>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-form-item label="价格">
                                <el-input type="textarea" v-model="station.price" :autosize="{ minRows: 2 }" placeholder="价格JSON"></el-input>
                            </el-form-item>
                        </el-card>
                    </el-col>
                </el-row>
            </el-form>
            <div slot="footer" class="add-dialog-footer">
                <el-button @click="addStation('add')" style="float: left;" :disabled="isAddLoading">添加站点</el-button>
                <el-button @click="addDialogVisible = false" :disabled="isAddLoading">取消</el-button>
                <el-button @click="addTrainSubmit" type="primary" :loading="isAddLoading">提交</el-button>
            </div>
        </el-dialog>
        <el-dialog title="编辑列车" :visible.sync="editDialogVisible" width="1000px" :close-on-click-modal="false" class="add-edit-dialog">
            <el-form :model="editForm" ref="editForm" label-width="auto" class="add-edit-form">
                <el-row :gutter="5">
                    <el-col :span="12" v-for="(station, index) in editForm.stations" :key="index">
                        <el-card shadow="never" class="stations-card">
                            <div class="title">
                                <span>第{{index + 1}}站</span>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <el-form-item label="车站">
                                        <el-select
                                            v-model="station.id"
                                            filterable
                                            remote
                                            size="small"
                                            placeholder="简拼/全拼/汉字"
                                            :remote-method="selectSearch"
                                            :loading="selectSearchLoading"
                                            class="input">
                                            <el-option
                                            v-for="item in selectSearchResult"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="车次">
                                        <el-input v-model="station.number" placeholder="车次" size="small" class="input"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="12">
                                    <el-form-item label="到时">
                                        <el-input-number placeholder="时" v-model="station.arriveTime[0]" controls-position="right" :min="0" :disabled="index == 0" size="small"></el-input-number>
                                        <el-input-number placeholder="分" v-model="station.arriveTime[1]" controls-position="right" :min="0" :disabled="index == 0" size="small"></el-input-number>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="12">
                                    <el-form-item label="发时">
                                        <el-input-number placeholder="时" v-model="station.departTime[0]" controls-position="right" :min="0" :disabled="index == editForm.stations.length - 1" size="small"></el-input-number>
                                        <el-input-number placeholder="分" v-model="station.departTime[1]" controls-position="right" :min="0" :disabled="index == editForm.stations.length - 1" size="small"></el-input-number>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-form-item label="价格">
                                <el-input type="textarea" v-model="station.price" :autosize="{ minRows: 2 }" placeholder="价格JSON" :disabled="index == editForm.stations.length - 1"></el-input>
                            </el-form-item>
                        </el-card>
                    </el-col>
                </el-row>
            </el-form>
            <div slot="footer" class="add-dialog-footer">
                <el-button @click="addStation('edit')" style="float: left;" :disabled="isEditLoading">添加站点</el-button>
                <el-button @click="editDialogVisible = false" :disabled="isEditLoading">取消</el-button>
                <el-button @click="editTrainSubmit" type="primary" :loading="isEditLoading">提交</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    name: 'Train',
    data() {
        return {
            trains: [],
            isTrainsLoading: false,
            timetableDialogVisible: false,
            timetable: {
                train_num: '',
                depart_station_num: '',
                arrive_station_num: '',
                stations: []
            },
            addDialogVisible: false,
            addForm: {
                stations: [
                    {
                        id: '',
                        number: '',
                        departTime: [],
                        arriveTime: [],
                        price: '{"2":{"2Z":100}}'
                    },
                    {
                        id: '',
                        number: '',
                        departTime: [],
                        arriveTime: [],
                        price: '{"2":{"2Z":100}}'
                    },
                ]
            },
            isAddLoading: false,
            editDialogVisible: false,
            editForm: {
                id: '',
                stations: []
            },
            isEditLoading: false,
            selectSearchLoading: false,
            selectSearchResult: [],
            paginate: {
                meta: {},
                links: {
                    prev: null,
                    next: null
                },
                page_now: 1
            }
        }
    },
    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$axios.get('/api/trains')
            .then(function (response) {
                vm.trains = response.data
                vm.paginate.meta = response.data.meta
                vm.paginate.links = response.data.links
                vm.isTrainsLoading = false
            })
        })
    },
    created() {
        this.$emit('getTitle', '车次管理');
        this.isTrainsLoading = true
    },
    methods: {
        changePage(page) {
            this.isTrainsLoading = true
            var _t = this
            _t.$axios.get('/api/trains?page=' + page)
            .then(function (response) {
                _t.trains = response.data
                _t.paginate.meta = response.data.meta
                _t.paginate.links = response.data.links
                _t.isTrainsLoading = false
            })
        },
        calcTimeDiff(train) {
            var departTime = train.stations[0].pivot.depart_time
            var arriveTime = train.stations[train.stations.length-1].pivot.arrive_time
            var hour = parseInt(arriveTime.substr(0,2)) - parseInt(departTime.substr(0,2))
            var min = parseInt(arriveTime.substr(3,5)) - parseInt(departTime.substr(3,5))
            var totalMin = hour * 60 + min
            return this.dataLeftCompleting(2,'0',Math.floor(totalMin/60)) + ':' + this.dataLeftCompleting(2,'0',(totalMin%60))
        },
        calcTimeDiff2(start, end) {
            var departTime = end
            var arriveTime = start
            var hour = parseInt(arriveTime.substr(0,2)) - parseInt(departTime.substr(0,2))
            var min = parseInt(arriveTime.substr(3,5)) - parseInt(departTime.substr(3,5))
            var totalMin = hour * 60 + min
            return this.dataLeftCompleting(2,'0',Math.floor(totalMin/60)) + ':' + this.dataLeftCompleting(2,'0',(totalMin%60))
        },
        openTimetableDialog(train) {
            this.timetable.train_num = JSON.parse(train.numbers)[0]
            this.timetable.depart_station_num = train.depart_station_num
            this.timetable.arrive_station_num = train.arrive_station_num
            this.timetable.stations = train.stations
            this.timetableDialogVisible = true
        },
        refreshTable() {
            this.isTrainsLoading = true
            this.$axios.get('/api/trains')
            .then(function (response) {
                this.trains = response.data
                this.isTrainsLoading = false
            })
        },
        selectSearch(query) {
            if (query !== '') {
                this.selectSearchLoading = true;
                var _t = this
                _t.$axios.post('/api/stations/search', {
                    content: query
                }).then((res) => {
                    this.selectSearchResult = res.data
                    this.selectSearchLoading = false
                }).catch((error) => {
                    this.$message({
                        showClose: true,
                        message: '搜索失败：' + error.response.data.message,
                        type: 'error'
                    });
                    this.selectSearchLoading = false
                })
            } else {
                this.selectSearchResult = [];
            }
        },
        addTrainSubmit() {
            
        },
        editTrain(train) {
            this.editForm.id = train.id
            for (let index = 0; index < train.stations.length; index++) {
                this.editForm.stations.push({
                        id: train.stations[index].id,
                        number: train.stations[index].pivot.train_num,
                        departTime: [
                            train.stations[index].pivot.depart_time ? train.stations[index].pivot.depart_time[0] + train.stations[index].pivot.depart_time[1] : null,
                            train.stations[index].pivot.depart_time ? train.stations[index].pivot.depart_time[3] + train.stations[index].pivot.depart_time[4] : null
                        ],
                        arriveTime: [
                            train.stations[index].pivot.arrive_time ? train.stations[index].pivot.arrive_time[0] + train.stations[index].pivot.arrive_time[1] : null,
                            train.stations[index].pivot.arrive_time ? train.stations[index].pivot.arrive_time[3] + train.stations[index].pivot.arrive_time[4] : null
                        ],
                        price: JSON.stringify(train.price[index + 1])
                    })
            }
            this.editDialogVisible = true
        },
        editTrainSubmit() {
            this.isEditLoading = true
            var stations = []
            var train_numbers = []
            var times = []
            var prices = {}
            for (let index = 0; index < this.editForm.stations.length; index++) {
                stations[index] = this.editForm.stations[index].id
                train_numbers[index] = this.editForm.stations[index].number
                times[index] = new Array()
                times[index][0] = index != 0 ? (this.dataLeftCompleting(2,"0",this.editForm.stations[index].arriveTime[0]) + ':' + this.dataLeftCompleting(2,"0",this.editForm.stations[index].arriveTime[1])) : null
                times[index][1] = index != this.editForm.stations.length - 1 ? this.dataLeftCompleting(2,"0",this.editForm.stations[index].departTime[0]) + ':' + this.dataLeftCompleting(2,"0",this.editForm.stations[index].departTime[1]) : null
                if (index < this.editForm.stations.length - 1) {
                    prices[index + 1] = this.editForm.stations[index].price ? JSON.parse(this.editForm.stations[index].price) : ''
                }
            }
            this.$axios.patch('/api/trains/' + this.editForm.id, {
                stations: JSON.stringify(stations),
                train_numbers: JSON.stringify(train_numbers),
                times: JSON.stringify(times),
                prices: JSON.stringify(prices)
            }).then((res) => {
                const result = res.data
                if (res.status == 200) {
                    this.isEditLoading = false
                    refreshTable()
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
                this.isEditLoading = false
            })
        },
        deleteTrain(train) {
            this.$axios.delete('/api/trains/' + train.id).then((res) => {
                if (res.status == 200) {
                    this.refreshTable()
                    this.$message({
                        showClose: true,
                        message: '删除成功',
                        type: 'success'
                    });
                } else {
                    this.$message({
                        showClose: true,
                        message: '删除失败，请稍后重试',
                        type: 'error'
                    });
                }
            }).catch((error) => {
                this.$message({
                    showClose: true,
                    message: '删除失败：' + error.response.data.message,
                    type: 'error'
                });
            })
        },
        addStation(type) {
            switch (type) {
                case 'add':
                    this.addForm.stations.push({
                        id: '',
                        number: '',
                        departTime: [],
                        arriveTime: [],
                        price: '{"2":{"2Z":100}}'
                    })
                    break;
            
                case 'edit':
                    this.editForm.stations.push({
                        id: '',
                        number: '',
                        departTime: [],
                        arriveTime: [],
                        price: '{"2":{"2Z":100}}'
                    })
                    break;
            }
        },
        dataLeftCompleting(bits, identifier, value) {
            value = Array(bits + 1).join(identifier) + value;
            return value.slice(-bits);
        },
        getDays(hours) {
            return [parseInt(hours/24), hours%24]
        }
    },
}
</script>

<style lang="less" scoped>
.page {
    height: calc(100% - 61px);
}
.train-table {
    span {
        display: block;
    }
}
.add-edit-form {
    .el-form-item {
        margin-bottom: 10px;
    }
    .stations-card {
        margin-bottom: 5px;
        .title {
            text-align: center;
            margin-bottom: 7px;
            font-size: 16px;
            font-weight: 500;
        }
    }
    .el-textarea {
        font-size: 12px;
    }
}
.pagination {
    text-align: center;
    margin-top: 20px;
}
</style>
<style lang="less">
.timetable-table,
.train-table {
    th, td {
        text-align: center;
    }
}
.train-table {
    .el-tag {
        font-size: 13px;
    }
}
.timetable-dialog {
    .el-dialog__body {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .timetable-dialog-footer {
        text-align: center;
    }
}
.add-edit-dialog {
    .el-dialog__body {
        padding: 10px 20px;
    }
}
.add-edit-form {
    .input {
        width: 163px;
    }
    .el-input-number {
        width: 80px;
        .el-input__inner {
            padding-left: 3px;
            padding-right: 24px;
        }
        .el-input-number__decrease,
        .el-input-number__increase {
            width: 20px;
        }
    }
    .stations-card {
        .el-card__body {
            height: unset;
            margin-top: unset;
            padding: 15px 20px;
        }
    }
    .el-textarea {
        textarea {
            font-family: monospace;
        }
    }
}
</style>