import React from 'react';
import { useEffect, useState } from 'react';
import { useFetchAllcode } from '../../customize/fetch';
import { DeleteAllcodeService, getListAllCodeService } from '../../../services/userService';
import moment from 'moment';
import { toast } from 'react-toastify';
import { PAGINATION } from '../../../utils/constant';
import ReactPaginate from 'react-paginate';
import FormSearch from '../../../component/Search/FormSearch';
import CommonUtils from '../../../utils/CommonUtils';
import {
    BrowserRouter as Router,
    Switch,
    Route,
    Link,
    Redirect
} from "react-router-dom";
import LoadingSpinner from '../../../component/Spinner/LoadingSpinner';
const ManageBrand = () => {
    const [keyword, setkeyword] = useState('')
    const [dataBrand, setdataBrand] = useState([])
    const [count, setCount] = useState('')
    const [numberPage, setnumberPage] = useState('')
    const [isLoading, setIsLoading] = useState(false);
    useEffect(() => {
        try {
           
            fetchData(keyword);
        } catch (error) {
            console.log(error)
        }

    }, [])
    let fetchData = async (keyword) => {
        setIsLoading(true); // Start loading
        let arrData = await getListAllCodeService({

            type: 'BRAND',
            limit: PAGINATION.pagerow,
            offset: 0,
            keyword:keyword

        })
        setIsLoading(false); // End loading after all fetches
        if (arrData && arrData.errCode === 0) {
            setdataBrand(arrData.data)
            setCount(Math.ceil(arrData.count / PAGINATION.pagerow))
        }
    }
    let handleDeleteBrand = async (event, id) => {
        event.preventDefault();
        setIsLoading(true); // Start loading
        let res = await DeleteAllcodeService(id)
        setIsLoading(false); // End loading after all fetches
        if (res && res.errCode === 0) {
            toast.success("Xóa nhãn hàng thành công")
            setIsLoading(true); // Start loading
            let arrData = await getListAllCodeService({

                type: 'BRAND',
                limit: PAGINATION.pagerow,
                offset: numberPage * PAGINATION.pagerow,
                keyword:keyword
            })
            setIsLoading(false); // End loading after all fetches
            if (arrData && arrData.errCode === 0) {
                setdataBrand(arrData.data)
                setCount(Math.ceil(arrData.count / PAGINATION.pagerow))
            }

        } else toast.error("Xóa nhãn hàng thất bại")
    }
    let handleChangePage = async (number) => {
        setnumberPage(number.selected)
        setIsLoading(true); // Start loading
        let arrData = await getListAllCodeService({

            type: 'BRAND',
            limit: PAGINATION.pagerow,
            offset: number.selected * PAGINATION.pagerow,
            keyword:keyword

        })
        setIsLoading(false); // End loading after all fetches
        if (arrData && arrData.errCode === 0) {
            setdataBrand(arrData.data)

        }
    }
    let handleSearchBrand = (keyword) =>{
        fetchData(keyword)
        setkeyword(keyword)
    }
    let handleOnchangeSearch = (keyword) =>{
        if(keyword === ''){
            fetchData(keyword)
            setkeyword(keyword)
        }
    }
    let handleOnClickExport =async () =>{
        setIsLoading(true); // Start loading
        let res = await getListAllCodeService({
            type: 'BRAND',
            limit: '',
            offset: '',
            keyword:''
        })
        setIsLoading(false); // End loading after all fetches
        if(res && res.errCode == 0){
            await CommonUtils.exportExcel(res.data,"Danh sách nhãn hàng","ListBrand")
        }
       
    }
    return (
        <div className="container-fluid px-4">
         {isLoading && <LoadingSpinner />}
            <h1 className="mt-4">Quản lý nhãn hàng</h1>


            <div className="card mb-4">
                <div className="card-header">
                    <i className="fas fa-table me-1" />
                    Danh sách nhãn hàng sản phẩm
                </div>
                <div className="card-body">
               
                    <div className='row'>
                    <div  className='col-4'>
                    <FormSearch title={"tên nhãn hàng"}  handleOnchange={handleOnchangeSearch} handleSearch={handleSearchBrand} />
                    </div>
                    <div className='col-8'>
                    <button  style={{float:'right'}} onClick={() => handleOnClickExport()} className="btn btn-success" >Xuất excel <i class="fa-solid fa-file-excel"></i></button>
                    </div>
                    </div>
                    <div className="table-responsive">
                        <table className="table table-bordered" style={{ border: '1' }} width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên</th>
                                    <th>mã code</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                {dataBrand && dataBrand.length > 0 &&
                                    dataBrand.map((item, index) => {
                                        return (
                                            <tr key={index}>
                                                <td>{index + 1}</td>
                                                <td>{item.value}</td>
                                                <td>{item.code}</td>
                                                <td>
                                                    <Link to={`/admin/edit-Brand/${item.id}`}>Edit</Link>
                                                    &nbsp; &nbsp;
                                                    <a href="#" onClick={(event) => handleDeleteBrand(event, item.id)} >Delete</a>
                                                </td>
                                            </tr>
                                        )
                                    })
                                }


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <ReactPaginate
                previousLabel={'Quay lại'}
                nextLabel={'Tiếp'}
                breakLabel={'...'}
                pageCount={count}
                marginPagesDisplayed={3}
                containerClassName={"pagination justify-content-center"}
                pageClassName={"page-item"}
                pageLinkClassName={"page-link"}
                previousLinkClassName={"page-link"}
                nextClassName={"page-item"}
                nextLinkClassName={"page-link"}
                breakLinkClassName={"page-link"}
                breakClassName={"page-item"}
                activeClassName={"active"}
                onPageChange={handleChangePage}
            />
        </div>
    )
}
export default ManageBrand;