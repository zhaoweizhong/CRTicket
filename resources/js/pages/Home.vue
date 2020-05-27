<template>
    <div class="page">
        <el-form :inline="true" ref="searchForm" :model="searchForm" :rules="searchRules" class="search-form">
            <el-form-item label="出发地" prop="departStation">
                <el-select
                    v-model="searchForm.departStation"
                    filterable
                    remote
                    size="medium"
                    placeholder="简拼/全拼/汉字"
                    :remote-method="selectSearch"
                    :loading="selectSearchLoading"
                    class="station-select">
                    <i class="far fa-map-marker-alt" slot="prefix"></i>
                    <el-option
                    v-for="item in selectSearchResult"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-tooltip class="item" effect="dark" content="调换出发地和目的地" placement="bottom">
                    <el-button size="medium" type="text" @click="handleReverse"><i class="fas fa-repeat"></i></el-button>
                </el-tooltip>
            </el-form-item>
            <el-form-item label="目的地" prop="arriveStation">
                <el-select
                    v-model="searchForm.arriveStation"
                    filterable
                    remote
                    size="medium"
                    placeholder="简拼/全拼/汉字"
                    :remote-method="selectSearch"
                    :loading="selectSearchLoading"
                    class="station-select">
                    <i class="far fa-map-marker-alt" slot="prefix"></i>
                    <el-option
                    v-for="item in selectSearchResult"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="出发日期" prop="date">
                <el-date-picker
                    size="medium"
                    v-model="searchForm.date"
                    type="date"
                    placeholder="选择日期"
                    :picker-options="datePickerOptions"
                    value-format="yyyy-MM-dd"
                    :clearable="false">
                </el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" size="medium" @click="onSearch" :loading="isTrainsLoading">查询</el-button>
            </el-form-item>
        </el-form>
        <el-table :data="trains" height="100%" stripe border class="train-table" v-loading="isTrainsLoading">
            <el-table-column label="车次" width="70">
                <template slot-scope="scope">
                    <span @click="openTimetableDialog(scope.row)">{{JSON.parse(scope.row.numbers)[0]}}</span>
                </template>
            </el-table-column>
            <el-table-column width="100">
                <template slot="header">
                    <span>出发站</span>
                    <span>到达站</span>
                </template>
                <template slot-scope="scope">
                    <span>{{scope.row.stations[scope.row.depart_station_num-1].name}}</span>
                    <span>{{scope.row.stations[scope.row.arrive_station_num-1].name}}</span>
                </template>
            </el-table-column>
            <el-table-column width="95">
                <template slot="header">
                    <span>出发时间</span>
                    <span>到达时间</span>
                </template>
                <template slot-scope="scope">
                    <span>{{scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time.substr(0,2))[1])+scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}{{scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time ? (getDays(scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time)[0] > 0 ? '（+'+getDays(scope.row.stations[scope.row.depart_station_num-1].pivot.depart_time.substr(0,2))[0]+'天）':'') : ''}}</span>
                    <span>{{scope.row.stations[scope.row.arrive_station_num-1].pivot.arrive_time ? ($moment(dataLeftCompleting(2,"0",getDays(scope.row.stations[scope.row.arrive_station_num-1].pivot.arrive_time.substr(0,2))[1])+scope.row.stations[scope.row.arrive_station_num-1].pivot.arrive_time.substr(2,), "HH:mm:ss").format('HH:mm')) : '-'}}{{scope.row.stations[scope.row.depart_station_num-1].pivot.arrive_time ? (getDays(scope.row.stations[scope.row.depart_station_num-1].pivot.arrive_time)[0] > 0 ? '（+'+getDays(scope.row.stations[scope.row.depart_station_num-1].pivot.arrive_time.substr(0,2))[0]+'天）':'') : ''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="历时" width="70">
                <template slot-scope="scope">
                    {{calcTimeDiff(scope.row)}}
                </template>
            </el-table-column>
            <el-table-column label="商务座" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['SZ'] ? ('余'+scope.row.seats_availability['SZ']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['SZ'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['SZ']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="一等座" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['1Z'] ? ('余'+scope.row.seats_availability['1Z']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['1Z'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['1Z']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="二等座" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['2Z'] ? ('余'+scope.row.seats_availability['2Z']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['2Z'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['2Z']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="软卧" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['RW'] ? ('余'+scope.row.seats_availability['RW']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['RW'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['RW']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="硬卧" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['YW'] ? ('余'+scope.row.seats_availability['YW']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['YW'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['YW']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column label="硬座" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.seats_availability['YZ'] ? ('余'+scope.row.seats_availability['YZ']+'张') : '-'}}</span>
                    <span>{{scope.row.seats_availability['YZ'] ? ('¥'+scope.row.price[scope.row.depart_station_num][scope.row.arrive_station_num]['YZ']):''}}</span>
                </template>
            </el-table-column>
            <el-table-column fixed="right" label="操作" width="100">
                <template slot-scope="scope">
                    <el-button type="success" size="mini" @click="router.push('/book?ti=' + scope.row.id + '&ds=' + scope.row.depart_station_id + '&as=' + scope.row.arrive_station_id + '&dt=' + searchForm.date)">预订</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-dialog :title="timetable.train_num + '次 时刻表'" :visible.sync="timetableDialogVisible" width="500px" class="timetable-dialog">
            <el-table :data="timetable.stations" height="100%" stripe border class="timetable-table" header-cell-class-name="table-cell" cell-class-name="table-cell">
                <el-table-column label="站序" width="61">
                    <template slot-scope="scope">
                        {{scope.row.pivot.station_num}}
                    </template>
                </el-table-column>
                <el-table-column label="车站" width="101">
                    <template slot-scope="scope">
                        <span :style="(scope.row.pivot.station_num == timetable.depart_station_num || scope.row.pivot.station_num == timetable.arrive_station_num) ? 'font-weight: 600;' : ''">{{scope.row.name}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="到站时间" width="99">
                    <template slot-scope="scope">
                        <span :style="(scope.row.pivot.station_num == timetable.arrive_station_num) ? 'font-weight: 600;' : ''">{{scope.row.pivot.arrive_time ? $moment(dataLeftCompleting(2,"0",getDays(scope.row.pivot.arrive_time.substr(0,2))[1])+scope.row.pivot.arrive_time.substr(2,), "HH:mm:ss").format('HH:mm') : '-'}}{{scope.row.pivot.arrive_time ? (getDays(scope.row.pivot.arrive_time)[0] > 0 ? '（+'+getDays(scope.row.pivot.arrive_time.substr(0,2))[0]+'天）':'') : ''}}</span>
                    </template>
                </el-table-column>
                <el-table-column label="出发时间" width="99">
                    <template slot-scope="scope">
                        <span :style="(scope.row.pivot.station_num == timetable.depart_station_num) ? 'font-weight: 600;' : ''">{{scope.row.pivot.depart_time ? $moment(dataLeftCompleting(2,"0",getDays(scope.row.pivot.depart_time.substr(0,2))[1])+scope.row.pivot.depart_time.substr(2,), "HH:mm:ss").format('HH:mm') : '-'}}{{scope.row.pivot.depart_time ? (getDays(scope.row.pivot.depart_time)[0] > 0 ? '（+'+getDays(scope.row.pivot.depart_time.substr(0,2))[0]+'天）':'') : ''}}</span>
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
    </div>
</template>

<script>
export default {
    name: 'Home',
    data() {
        return {
            searchForm: {
                departStation: '',
                arriveStation: '',
                date: ''
            },
            selectSearchLoading: false,
            selectSearchResult: [],
            datePickerOptions: {
                disabledDate(time) {
                    var now = new Date()
                    var today = now.setHours(0, 0, 0, 0);
                    return time.getTime() < today;
                },
            },
            searchRules: {
                departStation: [
                    { required: true, message: '请选择出发地', trigger: 'blur' },
                ],
                arriveStation: [
                    { required: true, message: '请选择目的地', trigger: 'blur' },
                ],
                date: [
                    { required: true, message: '请选择出发日期', trigger: 'blur' },
                ],
            },
            trains: [],
            isTrainsLoading: false,
            timetableDialogVisible: false,
            timetable: {
                train_num: '',
                depart_station_num: '',
                arrive_station_num: '',
                stations: []
            }
        }
    },
    created() {
        this.$emit('getTitle', '列车查询');
    },
    methods: {
        handleReverse() {
            var temp = this.searchForm.departStation
            this.searchForm.departStation = this.searchForm.arriveStation
            this.searchForm.arriveStation = temp
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
        onSearch() {
            this.$refs['searchForm'].validate((err) => {
                if (err) {
                    this.isTrainsLoading = true
                    this.$axios.post('/api/trains/search', {
                        depart_station: this.searchForm.departStation,
                        arrive_station: this.searchForm.arriveStation,
                        date: this.searchForm.date,
                    }).then((res) => {
                        const result = res.data
                        if (res.status == 200) {
                            this.isTrainsLoading = false
                            this.trains = result
                        } else {
                            this.isTrainsLoading = false
                            this.$message({
                                showClose: true,
                                message: '搜索失败，请稍后重试',
                                type: 'error'
                            });
                        }
                    }).catch((error) => {
                        this.$message({
                            showClose: true,
                            message: '搜索失败：' + error.response.data.message,
                            type: 'error'
                        });
                        this.isTrainsLoading = false
                    })
                }
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
        dataLeftCompleting(bits, identifier, value) {
            value = Array(bits + 1).join(identifier) + value;
            return value.slice(-bits);
        },
        getDays(hours) {
            return [parseInt(hours/24), hours%24]
        },
        openTimetableDialog(train) {
            this.timetable.train_num = JSON.parse(train.numbers)[0]
            this.timetable.depart_station_num = train.depart_station_num
            this.timetable.arrive_station_num = train.arrive_station_num
            this.timetable.stations = train.stations
            this.timetableDialogVisible = true
        }
    },
}
</script>

<style lang="less" scoped>
.page {
    height: calc(100% - 61px);
}
.search-form {
    text-align: center;
}
.train-table {
    span {
        display: block;
    }
}
</style>

<style lang="less">
.station-select {
    .el-input__prefix {
        left: 12px;
    }
}
.timetable-table,
.train-table {
    th, td {
        text-align: center;
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
</style>