import React from 'react';
import { useEffect, useState } from 'react';
import { useFetchAllcode } from '../../customize/fetch';
import { deleteSupplierService, getAllSupplier } from '../../../services/userService';
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

const ManageSupplier = () => {
    const [keyword, setkeyword] = useState('')
    const [dataSupplier, setdataSupplier] = useState([])
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
        console.log('daassda')
        let arrData = await getAllSupplier({

           
            limit: PAGINATION.pagerow,
            offset: 0,
            keyword:keyword

        })
        setIsLoading(false); // Set loading to false when all data is fetched
        if (arrData && arrData.errCode === 0) {
            setdataSupplier(arrData.data)
            setCount(Math.ceil(arrData.count / PAGINATION.pagerow))
        }
    }
    let handleDeleteSupplier = async (event, id) => {
        event.preventDefault();
        setIsLoading(true); // Start loading
        let res = await deleteSupplierService({
            data: {
                id: id
            }
        })
        setIsLoading(false); // Set loading to false when all data is fetched
        if (res && res.errCode === 0) {
            toast.success("Xóa nhà cung cấp thành công")
            setIsLoading(true); // Start loading
            let arrData = await getAllSupplier({

              
                limit: PAGINATION.pagerow,
                offset: numberPage * PAGINATION.pagerow,
                keyword:keyword
            })
            setIsLoading(false); // Set loading to false when all data is fetched
            if (arrData && arrData.errCode === 0) {
                setdataSupplier(arrData.data)
                setCount(Math.ceil(arrData.count / PAGINATION.pagerow))
            }

        } else toast.error("Xóa nhà cung cấp thất bại")
    }
    let handleChangePage = async (number) => {
        setnumberPage(number.selected)
        setIsLoading(true); // Start loading
        let arrData = await getAllSupplier({

          
            limit: PAGINATION.pagerow,
            offset: number.selected * PAGINATION.pagerow,
            keyword:keyword

        })
        setIsLoading(false); // Set loading to false when all data is fetched
        if (arrData && arrData.errCode === 0) {
            setdataSupplier(arrData.data)

        }
    }
    let handleSearchSupplier = (keyword) =>{
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
        let res = await getAllSupplier({
            limit: '',
            offset: '',
            keyword:''
        })
        setIsLoading(false); // Set loading to false when all data is fetched
        if(res && res.errCode == 0){
            await CommonUtils.exportExcel(res.data,"Danh sách nhà cung cấp","ListSupplier")
        }
       
    }
    return (
        <div className="container-fluid px-4">
                 {isLoading && <LoadingSpinner />}
            <h1 className="mt-4">Quản lý nhà cung cấp</h1>


            <div className="card mb-4">
                <div className="card-header">
                    <i className="fas fa-table me-1" />
                    Danh sách nhà cung cấp sản phẩm
                </div>
                <div className="card-body">
               
                    <div className='row'>
                    <div  className='col-4'>
                    <FormSearch title={"tên nhà cung cấp"}  handleOnchange={handleOnchangeSearch} handleSearch={handleSearchSupplier} />
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
                                    <th>Số điện thoại</th>
                                    <th>Email</th>
                                    <th>Địa chỉ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                {dataSupplier && dataSupplier.length > 0 &&
                                    dataSupplier.map((item, index) => {
                                        return (
                                            <tr key={index}>
                                                <td>{index + 1}</td>
                                                <td>{item.name}</td>
                                                <td>{item.phonenumber}</td>
                                                <td>{item.email}</td>
                                                <td>{item.address}</td>
                                                <td>
                                                    <Link to={`/admin/edit-Supplier/${item.id}`}>Edit</Link>
                                                    &nbsp; &nbsp;
                                                    <a href="#" onClick={(event) => handleDeleteSupplier(event, item.id)} >Delete</a>
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
export default ManageSupplier;