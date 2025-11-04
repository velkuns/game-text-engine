# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

```yaml
## [tag] - YYYY-MM-DD
[tag]: https://github.com/velkuns/game-text-engine/compare/0.2.1...master
### Changed
- Change 1
### Added
- Added 1
### Removed
- Remove 1
```



----

## [0.2.1] - 2025-10-30
[0.2.1]: https://github.com/velkuns/game-text-engine/compare/0.2.0...0.2.1
### Added
- Add ItemInterface::equip() method to equip an item
### Changed
- GameApi::read(): if source = target, do not apply the trigger (already be applied)
- Fix story test on condition for edge

## [0.2.0] - 2025-10-30
[0.2.0]: https://github.com/velkuns/game-text-engine/compare/0.1.0...0.2.0
### Added
- Add `isEnd` property on node when is node is end of graph
- Add GameApi::loadFromJsons() to load json directly
- Add GameApi::read() to handle read next step of story, with validation
- Add more tests
### Changed
- Remove GameApi properties "Api" suffix
- Update Readme

----

## [0.1.0] - 2025-10-29
[0.1.0]: https://github.com/velkuns/game-text-engine/compare/0.1.0...master
### Added
- Add Element system to manipulate and play
- Add Graph part to handle graph story
- Add API part to ease integration in clients
