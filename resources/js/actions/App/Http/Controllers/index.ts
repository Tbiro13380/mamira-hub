import DashboardController from './DashboardController'
import LetterController from './LetterController'
import BadgeController from './BadgeController'
import LeaderboardController from './LeaderboardController'
import ActivityController from './ActivityController'
import PhotoController from './PhotoController'
import StatsController from './StatsController'
import MemeController from './MemeController'
import QuizController from './QuizController'
import NotificationController from './NotificationController'
import ChatController from './ChatController'
import Settings from './Settings'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
LetterController: Object.assign(LetterController, LetterController),
BadgeController: Object.assign(BadgeController, BadgeController),
LeaderboardController: Object.assign(LeaderboardController, LeaderboardController),
ActivityController: Object.assign(ActivityController, ActivityController),
PhotoController: Object.assign(PhotoController, PhotoController),
StatsController: Object.assign(StatsController, StatsController),
MemeController: Object.assign(MemeController, MemeController),
QuizController: Object.assign(QuizController, QuizController),
NotificationController: Object.assign(NotificationController, NotificationController),
ChatController: Object.assign(ChatController, ChatController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers