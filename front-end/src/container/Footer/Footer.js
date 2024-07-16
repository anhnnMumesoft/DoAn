import React from 'react';

function Footer(props) {

  

  const rowStyle = {
    display: 'flex',
    justifyContent: 'center', // Căn giữa theo chiều ngang
  }

  return (
    <div >
      <footer className="footer-area section_gap">
        <div className="container">
        <div className="row" style={rowStyle}>
            <div className="col-lg-3 col-md-6 single-footer-widget">
              <h4>Giới thiệu </h4>
              <ul>
                <li><a href="#">Về NNA Shop </a></li>
                <li><a href="#">Tuyển dụng </a></li>
                <li><a href="#">Hệ thống cửa hàng </a></li>
              </ul>
            </div>
            <div className="col-lg-2 col-md-6 single-footer-widget">
              <h4>Dịch vụ chăm sóc khách hàng </h4>
              <ul>
                <li><a href="#">Chính sách điều khoản </a></li>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
                <li><a href="#">Chính sách thanh toán </a></li>
                <li><a href="#">Chính sách đổi trả </a></li>
                <li><a href="#">Chính sách bảo hành </a></li>
                <li><a href="#">Chính sách vận chuyển </a></li>
              </ul>
            </div>
            <div className="col-lg-3 col-md-6 single-footer-widget">
              <h4>Liên hệ </h4>
              <ul>
                <li><a href="#">Hotline: 0923960640</a></li>
                <li><a href="#">Email: anh.nn205228@sis.hust.edu.vn</a></li>
                {/* <li><a href="#"></a></li> */}
                {/* <li><a href="#">Terms of Service</a></li> */}
              </ul>
            </div>
            {/* <div className="col-lg-2 col-md-6 single-footer-widget">
              <h4>Resources</h4>
              <ul>
                <li><a href="#">Guides</a></li>
                <li><a href="#">Research</a></li>
                <li><a href="#">Experts</a></li>
                <li><a href="#">Agencies</a></li>
              </ul>
            </div> */}

          </div>
          <div className="footer-bottom row align-items-center">
            <p className="footer-text m-0 col-lg-8 col-md-12">{/* Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. */}
              Bản quyền ©2024 Đồ án tốt nghiệp của Nguyễn Ngọc Ánh <i className="fa fa-heart-o" aria-hidden="true" />  <a href="https://colorlib.com" target="_blank"></a>
              {/* Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. */}</p>

          </div>
        </div>
      </footer>

    </div>
  );
}

export default Footer;