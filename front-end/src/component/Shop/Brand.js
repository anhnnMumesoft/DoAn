import React, { useState, useEffect } from 'react';
import { Spinner } from 'react-bootstrap';
import { getAllCodeService } from '../../services/userService';
function Brand(props) {

    const [isLoading, setIsLoading] = useState(false);
    const [activeLinkId, setactiveLinkId] = useState('')
    const [arrBrand, setarrBrand] = useState([])
    let handleClickBrand = (code) => {
        props.handleRecevieDataBrand(code)
        setactiveLinkId(code)
    }
    useEffect(() => {
        let fetchBrand = async () => {
            setIsLoading(true); // Start loading
            let arrData = await getAllCodeService('BRAND')
            if (arrData && arrData.errCode === 0) {
                arrData.data.unshift({
                    createdAt: null,
                    code: "ALL",
                    type: "BRAND",
                    value: "Tất cả",
                })
                setarrBrand(arrData.data)
            }
            setIsLoading(false); // End loading
        }
        fetchBrand()
    }, [])
    return (

        <aside className="left_widgets p_filter_widgets">
            <div className="l_w_title">
                <h3>Các thương hiệu</h3>
            </div>
            <div className="widgets_inner">
                <ul className="list">
                    {arrBrand && arrBrand.length > 0 &&
                        arrBrand.map((item, index) => {
                            return (
                                <li className={item.code === activeLinkId ? 'active' : ''} style={{ cursor: 'pointer' }} onClick={() => handleClickBrand(item.code)} key={index}>
                                    <a >{item.value}</a>
                                </li>
                            )
                        })
                    }

                </ul>
            </div>
            {isLoading && (
            <div className="loading-overlay">
                <Spinner animation="border" variant="primary" />
            </div>
        )}
        </aside>

    );
}

export default Brand;