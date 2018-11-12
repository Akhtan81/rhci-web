import reducer from '../../../src/OrderEdit/reducers'

describe('`OrderEdit` index reducer', () => {

    it('`serverErrors` should return the initial state', () => {
        expect(reducer(undefined, {}).serverErrors).toEqual([])
    })

    it('`isSaveSuccess` should return the initial state', () => {
        expect(reducer(undefined, {}).isSaveSuccess).toEqual(false)
    })

    it('`isValid` should return the initial state', () => {
        expect(reducer(undefined, {}).isValid).toEqual(false)
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isLoading).toEqual(false)
    })

})