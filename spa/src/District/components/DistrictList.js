import React from 'react';
import {connect} from 'react-redux';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';

class DistrictList extends React.Component {

    // componentWillMount() {
    //     this.props.dispatch(FetchItems())
    // }

    render() {

        return <h4>Districts</h4>
    }
}

export default connect(selectors)(DistrictList)
