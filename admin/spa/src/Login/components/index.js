import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import {LOGIN_CREDENTIALS_CHANGED} from '../actions';
import selectors from './selectors';
import translator from '../../translations/translator';
import LoginCheck from '../actions/LoginCheck';
import {setTitle} from "../../Common/utils";

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
        const login = 'demo@ycombinatorllc.com'
        const password = 'a53b70b1f3504198500570d662b96048'

        this.props.dispatch({
            type: LOGIN_CREDENTIALS_CHANGED,
            payload: {
               login, password
            }
        });

        this.props.dispatch(LoginCheck(login, password))
    }

    render() {

        const {login, password, isValid, errors, isLoading} = this.props.Login

        return <div className="container">
            <div className="row">
                <div className="col col-sm-10 col-md-8 col-lg-6 offset-sm-1 offset-md-2 offset-lg-3 mx-auto">
                    <div className="card shadow-sm mt-4">
                        <div className="card-body">

                            <div className="row">
                                <div className="col-12 col-md-4">
                                    <div className="text-center">
                                        <img src="/img/favicon/apple-touch-icon-114x114.png"
                                             className="img-fluid mx-auto p-2"/>
                                    </div>
                                </div>
                                <div className="col-12 col-md-8">

                                    <h4 className="text-center text-md-left">{translator('login_title')}</h4>

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
                                <div className="col-12 col-md-6">
                                    <div className="text-center text-md-left">
                                        <p className="m-0">{translator('signin_already_registered')}
                                        &nbsp;<Link to="/register">{translator('signup')}</Link></p>
                                    </div>
                                </div>
                                <div className="col-12 col-md-6">
                                    <div className="text-center text-md-right">
                                        <p className="m-0"><Link to="/reset-password">{translator('login_reset_password')}</Link></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div className="card shadow-sm mt-2">
                        <div className="card-body">
                            <div className="row">
                                <div className="col-12 text-center">
                                    <h4>{translator('login_live_demo_title')}</h4>

                                    <button className="btn btn-success btn-lg"
                                            onClick={this.loginDemo}
                                            disabled={isLoading}>
                                        <i className="fa fa-flag-checkered"/>&nbsp;{translator('login_live_demo_action')}
                                    </button>
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
