import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import {LOGIN_CREDENTIALS_CHANGED} from '../actions';
import selectors from './selectors';
import translator from '../../translations/translator';
import LoginCheck from '../actions/LoginCheck';
import {setTitle} from "../../Common/utils";
import Logo from "../../Common/components/Logo";

class Login extends React.Component {

    componentWillMount() {
        setTitle(translator('login'))
    }

    submit = () => {

        const {login, password} = this.props.Login

        this.props.dispatch(LoginCheck(login, password))
    }

    submitIfEnter = e => {
        switch (e.keyCode) {
            case 13:
                this.submit()
        }
    }

    onChange = name => e => {
        this.props.dispatch({
            type: LOGIN_CREDENTIALS_CHANGED,
            payload: {
                [name]: e.target.value
            }
        })
    }

    onChangeIgnoreCase = name => e => {
        const value = e.target.value = e.target.value.toLowerCase()
        this.props.dispatch({
            type: LOGIN_CREDENTIALS_CHANGED,
            payload: {
                [name]: value
            }
        })
    }

    loginDemo = () => {
        const login = 'info@moveprola.com'
        const password = 'a53b70b1f3504198500570d662b96048'

        this.props.dispatch(LoginCheck(login, password))
    }

    render() {

        const {login, password, isValid, errors, isLoading} = this.props.Login

        return <div className="container">

            <Logo/>

            <div className="row mb-5">
                <div className="col col-sm-10 col-md-6 col-lg-5 mx-auto">

                    <div className="card shadow-sm mb-2">
                        <div className="card-body">

                            <div className="row">
                                <div className="col-12">

                                    <h4 className="text-center">{translator('login_title')}</h4>

                                    {errors.length > 0 && <div className="alert alert-danger">
                                        <ul className="simple">{errors.map((e, i) => <li key={i}>
                                            <small>{e}</small>
                                        </li>)}</ul>
                                    </div>}

                                    <div className="form-group">
                                        <input type="text"
                                               className="form-control text-center"
                                               name="login"
                                               autoFocus={true}
                                               placeholder={translator('login')}
                                               onChange={this.onChangeIgnoreCase('login')}
                                               onKeyDown={this.submitIfEnter}
                                               value={login || ''}/>
                                    </div>
                                    <div className="form-group">
                                        <input type="password"
                                               className="form-control text-center"
                                               name="password"
                                               placeholder={translator('password')}
                                               onChange={this.onChange('password')}
                                               onKeyDown={this.submitIfEnter}
                                               value={password || ''}/>
                                    </div>
                                    <div className="form-group text-center">
                                        <button className="btn btn-primary"
                                                onClick={this.submit}
                                                disabled={!isValid || isLoading}>
                                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-lock"}/>
                                            &nbsp;{translator('login_action')}
                                        </button>
                                    </div>

                                </div>
                            </div>

                            <div className="row">
                                <div className="col-12 col-md-6 ml-auto">
                                    <div className="text-center text-md-right">
                                        <p className="m-0"><Link
                                            to="/reset-password">{translator('login_reset_password')}</Link></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div className="card shadow-sm mb-2">
                        <div className="card-body">
                            <div className="row text-center">
                                <div className="col-12">
                                    <h4>{translator('signin_already_registered')}</h4>
                                </div>
                                <div className="col-10 col-md-8 mx-auto">
                                    <Link className="btn btn-success btn-lg btn-block"
                                          to="/introduction">{translator('navigation_partners_register')}</Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card shadow-sm mb-2">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-12 text-center">
                                    <div className="col-12">
                                        <h4>{translator('login_live_demo_title')}</h4>
                                    </div>
                                    <div className="col-10 col-md-8 mx-auto">
                                        <button className="btn btn-success btn-lg btn-block"
                                                onClick={this.loginDemo}
                                                disabled={isLoading}>
                                            {translator('login_live_demo_action')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Login))
