import reducer from '../../../src/OrderEdit/reducers/Chat'

describe('`OrderEdit` Chat reducer', () => {

    it('`messages` should return the initial state', () => {
        expect(reducer(undefined, {}).messages).toEqual([])
    })
})