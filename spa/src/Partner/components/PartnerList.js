import React from 'react';
import {connect} from 'react-redux';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';

class PartnerList extends React.Component {

    // componentWillMount() {
    //     this.props.dispatch(FetchItems())
    // }

    render() {

        return <h4>Partners</h4>
    }
}

export default connect(selectors)(PartnerList)
