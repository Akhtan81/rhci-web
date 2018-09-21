import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect, Link} from 'react-router-dom';
import {LOGIN_CREDENTIALS_CHANGED} from '../actions';
import selectors from './selectors';
import translator from '../../translations/translator';
import LoginCheck from '../actions/LoginCheck';
import {setTitle} from "../../Common/utils";

class Login extends React.Component {

    state = {
        redirectToReferrer: false
    }

    componentWillMount() {

        setTitle(translator('login'))
    }

    submit = () => {

        const {login, password} = this.props.Login

        this.props.dispatch(LoginCheck(login, password, () => {
            this.setState({
                redirectToReferrer: true
            })
        }))
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

    render() {

        const {redirectToReferrer} = this.state

        if (redirectToReferrer === true) {
            return <Redirect to='/'/>
        }

        const {login, password, isValid, errors, isLoading} = this.props.Login

        return <div className="container">
            <div className="row">
                <div className="col col-sm-10 col-md-6 offset-sm-1 offset-md-3 mx-auto">
                    <div className="card shadow-sm mt-4">
                        <div className="card-body">

                            <div className="row mb-4">
                                <div className="col-12 col-lg-4">
                                    <div className="text-center">
                                        <img src="/img/favicon/apple-touch-icon-114x114.png"
                                             className="img-fluid mx-auto p-2"/>
                                    </div>
                                </div>
                                <div className="col-12 col-lg-8">

                                    <h4>{translator('login_title')}</h4>

                                    {errors.length > 0 && <div className="alert alert-danger">
                                        <ul className="simple">{errors.map((e, i) => <li key={i}><small>{e}</small></li>)}</ul>
                                    </div>}

                                    <div className="form-group">
                                        <input type="text"
                                               className="form-control text-center"
                                               name="login"
                                               autoFocus={true}
                                               onChange={this.onChange('login')}
                                               onKeyDown={this.submitIfEnter}
                                               value={login || ''}/>
                                    </div>
                                    <div className="form-group">
                                        <input type="password"
                                               className="form-control text-center"
                                               name="password"
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


                            <div className="mb-2">
                                <p>{translator('signin_already_registered')}&nbsp;<Link
                                    to="/register">{translator('signup')}</Link></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Login))
