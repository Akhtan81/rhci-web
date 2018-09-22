import React from 'react';
import {connect} from 'react-redux';
import {Link, Redirect, withRouter} from 'react-router-dom';
import {MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";

class PasswordSet extends React.Component {

    state = {
        canRedirect: false
    }

    componentWillMount() {
        setTitle(translator('navigation_set_password'))

        const {token} = this.props.match.params;

        if (token)
            this.change('token', token)
    }

    componentWillUnmount() {
        this.change('token', null)
    }

    submit = () => {
        const {model} = this.props.PasswordSet

        this.props.dispatch(Save(model, () => {
            this.setState({
                canRedirect: true
            })
        }))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.PasswordSet.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        if (this.state.canRedirect) {
            return <Redirect to="/login"/>
        }

        const {model, isLoading, isValid, serverErrors} = this.props.PasswordSet

        return <div className="container">
            <div className="row">
                <div className="col-12 col-sm-8 col-md-6 offset-sm-2 offset-md-3">
                    <div className="card shadow-sm my-4">
                        <div className="card-body">

                            <h2 className="text-center">{translator('navigation_set_password')}</h2>

                            <p>{translator('reset_password_remember')}
                                &nbsp;<Link to="/login">{translator('signin')}</Link></p>

                            {serverErrors.length > 0 && <div className="alert alert-danger">
                                <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                            </div>}

                            <div className="row">
                                <div className="col-12">

                                    <div className="form-group">
                                        <label className="required">{translator('password')}</label>
                                        <input type="password"
                                               name="password"
                                               className="form-control"
                                               onChange={this.changeString('password')}
                                               value={model.user.password || ''}/>
                                        {this.getError('password')}
                                    </div>

                                </div>
                                <div className="col-12">

                                    <div className="form-group">
                                        <label className="required">{translator('password_repeat')}</label>
                                        <input type="password"
                                               name="password2"
                                               className="form-control"
                                               onChange={this.changeString('password2')}
                                               value={model.user.password2 || ''}/>
                                        {this.getError('password2')}
                                    </div>

                                </div>

                                <div className="col-12">
                                    <div className="form-group text-center">
                                        <button className="btn btn-success"
                                                onClick={this.submit}
                                                disabled={isLoading || !isValid}>
                                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-key"}/>
                                            &nbsp;{translator('confirm')}
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

export default withRouter(connect(selectors)(PasswordSet))
