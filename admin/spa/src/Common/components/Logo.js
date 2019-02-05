import React from 'react'
import translator from "../../translations/translator";

const logoStyle = {maxWidth: '100px'}

const Logo = () => <div className="row my-2">
    <div className="col-12">
        <div className="mx-auto">
            <div className="text-center">
                <a href="https://mobilerecycling.net">
                    <img src="/img/logo.png" style={logoStyle}
                         className="img-fluid mx-auto p-2"/>
                </a>
            </div>
        </div>
        <div className="col-12">
            <p className="h2 text-center c-green-mrs">{translator('login_logo_title')}</p>
        </div>
    </div>
</div>

export default Logo